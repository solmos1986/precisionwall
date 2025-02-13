class porcentajes {
    amarillo = {
        fondo: 'rgba(255, 206, 86, 0.2)',
        borde: 'rgba(255, 206, 86, 1)'
    }
    verde = {
        fondo: 'rgba(75, 192, 192, 0.2)',
        borde: 'rgba(75, 192, 192, 1)'
    }
    rojo = {
        fondo: 'rgba(255, 99, 132, 0.2)',
        borde: 'rgba(255, 99, 132, 1)',
    }

    constructor(response) {
        this.response = response;
    }
    /* graficos para tareas */
    mostrar_tareas(){
        return {
            label:`# Hours worked `,
            data: [this.response.porcentaje_horas_completadas],
            backgroundColor: [
                this.verde.fondo,

            ],
            borderColor: [
                this.verde.borde,
            ],
            borderWidth: 1
        }
    }
    labels(datos){
        var label=[];
        datos.forEach(data => {
            label.push(data.Nombre.trim());
        });
        return label;
    }
    dataSet(datos,label,color,tipo){
        var data=[];
        switch (tipo) {
            case 'porcetajes horas trabajadas':
                datos.forEach(dato => {
                    data.push(dato.porcentaje_horas_completadas);
                });
                break;
            case 'horas restantes':
                datos.forEach(dato => {
                    data.push(dato.horas_restantes);
                });
                break;
            case 'horas trabajadas':
                datos.forEach(dato => {
                    data.push(dato.horas_trabajadas);
                });
                break;
            default:
                break;
        }
        var dataset={
            label: label,
            data: data,
            backgroundColor: [
                color.fondo,
                color.fondo,
                color.fondo
            ],
            borderColor: [
                color.borde,
                color.borde,
                color.borde        
            ],
            borderWidth: 1
        }
        return dataset;
    }
    /* graficos para areas */
    mostrar_areas(){
        return {
            labels:this.labels(this.response.tareas),
            datasets: [
                this.dataSet(this.response.tareas,'% completed',this.verde,'porcetajes horas trabajadas'),
            ]
        }
    }
    mostrar_pisos(){
        return {
            labels:this.labels(this.response.areas_control),
            datasets: [
                this.dataSet(this.response.areas_control,'% completed',this.verde,'porcetajes horas trabajadas'),
            ]
        }
    }
    mostrar_proyecto(){
        return {
            labels:this.labels(this.response.floors),
            datasets: [
                this.dataSet(this.response.floors,'% completed',this.verde,'porcetajes horas trabajadas'),
            ]
        }
    }
    mostrar_empresa(){
        return {
            labels:this.labels(this.response.proyectos),
            datasets: [
                this.dataSet(this.response.proyectos,'% completed',this.verde,'porcetajes horas trabajadas'),
            ]
        }
    }
}