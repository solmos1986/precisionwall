class Charbarras {
    amarillo = {
        fondo: 'rgba(246, 255, 147, 0.2)',
        borde: 'rgba(255, 206, 86, 1)',
    }
    amarillo_intenso = {
        fondo: 'rgba(248, 251, 85, 0.4)',
        borde: 'rgba(255, 206, 86, 1)',
    }
    verde_intenso = {
        fondo: 'rgba(128, 128, 128, 0.4)',
        borde: 'rgba(128, 130, 138, 0.6)'
    }
    verde = {
        fondo: 'rgba(138, 140, 149, 0.2)',
        borde: 'rgba(128, 130, 138, 0.5)'
    }
    rojo = {
        fondo: 'rgba(255, 99, 132, 0.2)',
        borde: 'rgba(255, 99, 132, 0.5)',
    }
    rojo_intenso = {
        fondo: 'rgba(255, 45, 49, 0.3)',
        borde: 'rgba(255, 45, 49, 0.5)',
    }
    orange = {
        fondo: 'rgba(255, 159, 64, 0.2)',
        borde: 'rgba(255, 159, 64, 1)',
    }
    blue = {
        fondo: 'rgba(54, 162, 235, 0.2)',
        borde: 'rgba(54, 162, 235, 1)',
    }

    constructor(response) {
        this.response = response;
    }
    mostrar_tareas() {
        /* validar colores */
        const horas_restantes = this.verficar_negativo(this.response.horas_restantes);
        const horas_completadas = this.verficar_completadas(this.response.horas_completadas, this.response.horas_trabajadas);
        return {
            label: `#Task ${this.response.Nombre.trim()}`,
            data: [
                this.response.horas_estimadas,
                this.response.horas_trabajadas,
                horas_restantes.horas_restantes,
                horas_completadas.horas_restantes,
                this.response.horas_trabajadas,
                this.response.porcentaje_horas_completadas,
                this.response.porcentaje_horas_trabajadas,
            ],
            backgroundColor: [
                this.amarillo_intenso.fondo,
                this.amarillo.fondo,
                horas_restantes.fondo,
                horas_completadas.fondo,
                this.amarillo.fondo
            ],
            borderColor: [
                this.amarillo.borde,
                this.amarillo.borde,
                horas_restantes.borde,
                horas_completadas.borde,
                this.amarillo.borde
            ],
            borderWidth: 1
        }
    }
    mostrar_resumen() {
        const horas_restantes = this.verficar_negativo(this.response.horas_restantes);
        const horas_completadas = this.verficar_completadas(this.response.horas_completadas, this.response.horas_trabajadas);
        return {
            label: `# Summary: ${this.response.Nombre.trim()}`,
            data: [
                this.response.horas_estimadas,
                this.response.horas_trabajadas,
                horas_restantes.horas_restantes,
                horas_completadas.horas_restantes,
                this.response.horas_trabajadas,
                this.response.porcentaje_horas_completadas,
                this.response.porcentaje_horas_trabajadas,
            ],
            backgroundColor: [
                this.amarillo_intenso.fondo,
                this.amarillo.fondo,
                horas_restantes.fondo,
                horas_completadas.fondo,
                this.amarillo.fondo
            ],
            borderColor: [
                this.amarillo.borde,
                this.amarillo.borde,
                horas_restantes.borde,
                horas_completadas.borde,
                this.amarillo.borde
            ],
            borderWidth: 1
        }
    }
    labels(datos) {
        var label = [];
        datos.forEach(data => {
            label.push(data.Nombre.trim());
        });
        return label;
    }
    dataSet(datos, label, color, tipo) {
        var data = [];
        switch (tipo) {
            case 'horas estimadas':
                datos.forEach(dato => {
                    data.push(dato.horas_estimadas);
                });
                break;
            case 'horas restantes':
                datos.forEach(dato => {
                    //var resultado=this.verficar_negativo(dato.horas_restantes);
                    data.push(dato.horas_restantes);
                    /*  color.fondo=resultado.fondo
                     color.borde=resultado.borde */
                });
                break;
            case 'horas trabajadas':
                datos.forEach(dato => {
                    data.push(dato.horas_trabajadas);
                });
                break;
            case 'porcentaje horas completadas':
                datos.forEach(dato => {
                    data.push(dato.horas_completadas);
                });
                break;
            case 'porcentaje horas trabajadas':
                datos.forEach(dato => {
                    data.push(dato.horas_trabajadas);
                });
                break;
            case 'horas completadas':
                datos.forEach(dato => {
                    data.push(dato.porcentaje_horas_completadas);
                });
                break;
            case 'horas usadas':
                datos.forEach(dato => {
                    data.push(dato.porcentaje_horas_trabajadas);
                });
                break;
            default:
                break;
        }
        console.log(color.fondo,
            color.borde);
        var dataset = {
            label: label,
            data: data,
            backgroundColor: [
                color.fondo
            ],
            borderColor: [
                color.borde,

            ],
            borderWidth: 1
        }
        return dataset;
    }
    /* mostrar_areas() {
        return {
            labels: this.labels(this.response.tareas),
            datasets: [
                this.dataSet(this.response.tareas, '#Hours Est', this.amarillo_intenso, 'horas estimadas'),
                this.dataSet(this.response.tareas, '#Hours Used', this.amarillo, 'horas trabajadas'),
                this.dataSet(this.response.tareas, '#Hours Left', this.rojo, 'horas restantes'),
                this.dataSet(this.response.tareas, '% completed', this.orange, 'porcentaje horas completadas'),
                this.dataSet(this.response.tareas, '% job', this.amarillo, 'porcentaje horas trabajadas'),
               
                this.dataSet(this.response.tareas, 'none', 'none', 'horas completadas'),
                this.dataSet(this.response.tareas, 'none', 'none', 'horas usadas'),
            ]
        }
    }
    mostrar_pisos() {
        return {
            labels: this.labels(this.response.areas_control),
            datasets: [
                this.dataSet(this.response.areas_control, '#Hours Est', this.amarillo, 'horas estimadas'),
                this.dataSet(this.response.areas_control, '#Hours Used', this.verde, 'horas trabajadas'),
                this.dataSet(this.response.areas_control, '#Hours Left', this.rojo, 'horas restantes'),
                this.dataSet(this.response.areas_control, '% completed', this.orange, 'porcentaje horas completadas'),
                this.dataSet(this.response.areas_control, '% job', this.blue, 'porcentaje horas trabajadas'),
     
                this.dataSet(this.response.areas_control, 'none', 'none', 'horas completadas'),
                this.dataSet(this.response.areas_control, 'none', 'none', 'horas usadas'),
            ]
        }
    }
    mostrar_proyecto() {
        return {
            labels: this.labels(this.response.floors),
            datasets: [
                this.dataSet(this.response.floors, '#Hours Est', this.amarillo, 'horas estimadas'),
                this.dataSet(this.response.floors, '#Hours Used', this.verde, 'horas trabajadas'),
                this.dataSet(this.response.floors, '#Hours Left', this.rojo, 'horas restantes'),
                this.dataSet(this.response.floors, '% completed', this.orange, 'porcentaje horas completadas'),
                this.dataSet(this.response.floors, '% job', this.blue, 'porcentaje horas trabajadas'),
            
                this.dataSet(this.response.floors, 'none', 'none', 'horas completadas'),
                this.dataSet(this.response.floors, 'none', 'none', 'horas usadas'),
            ]
        }
    }
    mostrar_empresa() {
        return {
            labels: this.labels(this.response.proyectos),
            datasets: [
                this.dataSet(this.response.proyectos, '#Hours Est', this.amarillo, 'horas estimadas'),
                this.dataSet(this.response.proyectos, '#Hours Used', this.verde, 'horas trabajadas'),
                this.dataSet(this.response.proyectos, '#Hours Left', this.rojo, 'horas restantes'),
                this.dataSet(this.response.proyectos, '% completed', this.orange, 'porcentaje horas completadas'),
                this.dataSet(this.response.proyectos, '% job', this.blue, 'porcentaje horas trabajadas'),
               
                this.dataSet(this.response.proyectos, 'none', 'none', 'horas completadas'),
                this.dataSet(this.response.proyectos, 'none', 'none', 'horas usadas'),

            ]
        }
    } */
    /* funciones extras */
    verficar_negativo(x) {
        var resultado = Math.sign(x)
        if (resultado == -1) {
            return resultado = {
                horas_restantes: x,
                fondo: this.rojo.fondo,
                borde: this.rojo.borde,
                color: 'rojo'
            }
        } else {
            return resultado = {
                horas_restantes: x,
                fondo: this.verde.fondo,
                borde: this.verde.borde,
                color: 'verde'
            }
        }
    }
    /* funciones extras */
    verficar_completadas(completadas, usadas) {
        var resultado;
        if (completadas >= usadas) {
            return resultado = {
                horas_restantes: completadas,
                fondo: this.verde_intenso.fondo,
                borde: this.verde_intenso.borde,
                color: 'verde'
            }
        } else {
            return resultado = {
                horas_restantes: completadas,
                fondo: this.rojo_intenso.fondo,
                borde: this.rojo_intenso.borde,
                color: 'rojo'
            }
        }
    }


    destructure(tipo) {
        switch (tipo) {
            case 'tarea':
                var data = this.response.tareas
                break;
            case 'areas_control':
                var data = this.response.areas_control
                break;
            case 'floor':
                var data = this.response.floors
                break;
            case 'proyecto':
                var data = this.response.proyectos
                break;

            default:
                break;
        }

        return {
            labels: data.map(tarea => tarea.Nombre), //data task 
            datasets: [{
                label: 'Hours Est', //horas estimadas 
                data: data.map((tarea) => {
                    return tarea.horas_estimadas;
                }),
                parsing: {
                    yAxisKey: 'net'
                },
                backgroundColor: [
                    this.amarillo_intenso.fondo,
                ],
                borderColor: [
                    this.amarillo_intenso.borde
                ],
                borderWidth: 1
            }, {
                label: 'Hours Used', //horas usadas 
                data: data.map(
                    (tarea) => {
                        //console.log( tarea.horas_estimadas);
                        return tarea.horas_trabajadas
                    }),
                parsing: {
                    yAxisKey: 'cogs'
                },
                backgroundColor: [
                    this.amarillo.fondo,
                ],
                borderColor: [
                    this.amarillo.borde
                ],
                borderWidth: 1
            },
            {
                label: 'Hours left', //horas no usadas 
                data: data.map(
                    (tarea) => {
                        return tarea.horas_restantes
                    }),
                parsing: {
                    yAxisKey: 'gm'
                },
                backgroundColor: data.map(
                    (tarea) => {
                        const resultado = this.verficar_negativo(tarea.horas_restantes)
                        return resultado.fondo
                    }),
                borderColor: data.map(
                    (tarea) => {
                        const resultado = this.verficar_negativo(tarea.horas_restantes)
                        return resultado.borde
                    }),
                borderWidth: 1
            },
            {
                label: '% completed', //horas no usadas 
                data: data.map(
                    (tarea) => {
                        //console.log( tarea.horas_completadas);
                        return tarea.horas_completadas
                    }),
                parsing: {
                    yAxisKey: 'gm'
                },
                backgroundColor: data.map(
                    (tarea) => {
                        const resultado = this.verficar_completadas(tarea.horas_completadas, tarea.horas_trabajadas)
                        return resultado.fondo
                    }),
                borderColor: data.map(
                    (tarea) => {
                        const resultado = this.verficar_negativo(tarea.horas_completadas, tarea.horas_trabajadas)
                        return resultado.borde
                    }),
                borderWidth: 1
            },
            {
                label: '% Hours Used', //horas no usadas 
                data: data.map(
                    (tarea) => {
                        //console.log( tarea.horas_restantes);
                        return tarea.horas_trabajadas
                    }),
                parsing: {
                    yAxisKey: 'gm'
                },
                backgroundColor: [
                    this.amarillo.fondo,
                ],
                borderColor: [
                    this.amarillo.borde
                ],
                borderWidth: 1
            }
            ]
        }
    }
    /* funciones extras 
evaluando %completado y %usado */
    verficar_proyectos_malos() {
        var proyectos = [];
        console.log(this.response.proyectos)
        this.response.proyectos.forEach(proyecto => {
            if (proyecto.horas_completadas >= proyecto.horas_trabajadas) { } else {
                //malo
                proyectos.push(proyecto);
            }
        });
        this.response.proyectos = proyectos;
        return this.destructure('proyecto');

    }
    verficar_proyectos_buenos() {
        var proyectos = [];
        console.log(this.response.proyectos)
        this.response.proyectos.forEach(proyecto => {
            if (proyecto.horas_completadas >= proyecto.horas_trabajadas) {
                //bueno
                proyectos.push(proyecto);
            }
        });
        this.response.proyectos = proyectos;
        console.log(this.response.proyectos);
        return this.destructure_verificar(this.response.proyectos);
    }
    destructure_verificar(data) {
        console.log(data);
        return {
            labels: data.map(tarea => tarea.Nombre), //data task 
            datasets: [{
                label: 'Hours Est', //horas estimadas 
                data: data.map((tarea) => {
                    return tarea.horas_estimadas;
                }),
                parsing: {
                    yAxisKey: 'net'
                },
                backgroundColor: [
                    this.amarillo_intenso.fondo,
                ],
                borderColor: [
                    this.amarillo_intenso.borde
                ],
                borderWidth: 1
            }, {
                label: 'Hours Used', //horas usadas 
                data: data.map(
                    (tarea) => {
                        //console.log( tarea.horas_estimadas);
                        return tarea.horas_trabajadas
                    }),
                parsing: {
                    yAxisKey: 'cogs'
                },
                backgroundColor: [
                    this.amarillo.fondo,
                ],
                borderColor: [
                    this.amarillo.borde
                ],
                borderWidth: 1
            },
            {
                label: 'Hours left', //horas no usadas 
                data: data.map(
                    (tarea) => {
                        return tarea.horas_restantes
                    }),
                parsing: {
                    yAxisKey: 'gm'
                },
                backgroundColor: data.map(
                    (tarea) => {
                        const resultado = this.verficar_negativo(tarea.horas_restantes)
                        return resultado.fondo
                    }),
                borderColor: data.map(
                    (tarea) => {
                        const resultado = this.verficar_negativo(tarea.horas_restantes)
                        return resultado.borde
                    }),
                borderWidth: 1
            },
            {
                label: '% completed', //horas no usadas 
                data: data.map(
                    (tarea) => {
                        //console.log( tarea.horas_completadas);
                        return tarea.horas_completadas
                    }),
                parsing: {
                    yAxisKey: 'gm'
                },
                backgroundColor: data.map(
                    (tarea) => {
                        const resultado = this.verficar_completadas(tarea.horas_completadas, tarea.horas_trabajadas)
                        return resultado.fondo
                    }),
                borderColor: data.map(
                    (tarea) => {
                        const resultado = this.verficar_negativo(tarea.horas_completadas, tarea.horas_trabajadas)
                        return resultado.borde
                    }),
                borderWidth: 1
            },
            {
                label: '% Used', //horas no usadas 
                data: data.map(
                    (tarea) => {
                        //console.log( tarea.horas_restantes);
                        return tarea.horas_trabajadas
                    }),
                parsing: {
                    yAxisKey: 'gm'
                },
                backgroundColor: [
                    this.amarillo.fondo,
                ],
                borderColor: [
                    this.amarillo.borde
                ],
                borderWidth: 1
            }]
        }
    }
}