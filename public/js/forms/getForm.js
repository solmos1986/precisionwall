class GetForm {
    resultado = new Object(
        
    );
    getQuestion() {
        var total = $('#contenedor').children().length;
        console.log(total);
        //secciones
        let secciones = [];
        $('#contenedor').children().each((index, ele) => {
            //console.log(ele);

            if (index !== 0 && index !== (total - 1)) { //obteniedo el contenido
                var datos = $(ele).children().children().children()[0];
                var name_question = $(datos).children()[0];

                name_question = $(name_question).children()[0]; //get question destroy row
                name_question = $(name_question).children()[0]; // destroy row
                if ($(name_question).hasClass('descripcion')) {

                    const subtitulo = $(name_question).val();
                    var buscando = $(ele).children().children().children()[0];
                    var descripcion = $(buscando).children()[1];

                    descripcion = $(descripcion).children()[0]; //get decripcion destroy row
                    descripcion = $(descripcion).children()[0];
                    descripcion = $(descripcion).val();

                    var obteniedo_preguntas = $(datos).parent().next().children();
                    const limite = obteniedo_preguntas.length;

                    let pregunta = []
                    let tipo;
                    obteniedo_preguntas.each((index, ele) => {
                        let respuestas = []
                        if (index < (limite - 2)) {
                            var obteniedo_rows = $(ele).children()[0];
                            obteniedo_rows = $(obteniedo_rows).children()[0];
                            obteniedo_rows = $(obteniedo_rows).children()[0];
                            obteniedo_rows = $(obteniedo_rows).children()[0];
                            var obteniendo_respuestas = $(obteniedo_rows).next().next();
                            obteniedo_rows = $(obteniedo_rows).children()[0];
                            obteniedo_rows = $(obteniedo_rows).children()[0];
                            //encontrando tipo option
                            $(obteniendo_respuestas).children().each((index, elem) => {

                                //comprovando q option es
                                var option_check_box = $(elem).hasClass('option_check_box')
                                var option_box = $(elem).hasClass('option_box')
                                var option_paragraph = $(elem).hasClass('option_paragraph')
                                var option_scale = $(elem).hasClass('option_scale')
                                //call methods
                                if (option_check_box) {
                                    tipo = "check";
                                    const res = this.get_checkbox(elem, (index + 1));
                                    respuestas.push(res);
                                }
                                if (option_box) {
                                    tipo = "box";
                                    const res = this.get_box(elem, (index + 1))
                                    respuestas.push(res);
                                }
                                if (option_paragraph) {
                                    tipo = "text";
                                    const res = this.get_text(elem, (index + 1))
                                    respuestas.push(res);
                                }
                                if (option_scale) {
                                    tipo = "escala";
                                    const res = this.get_scale(elem, (index + 1))
                                    respuestas = res;
                                }
                            });

                            pregunta.push({
                                pregunta: $(obteniedo_rows).val(),
                                tipo: tipo,
                                respuestas: respuestas
                            });
                        }
                    });
                    secciones.push({
                        subtitulo: subtitulo,
                        descripcion: descripcion,
                        preguntas: pregunta
                    });
                }

            }

        });
        this.resultado = {
            title: $('#title').val(),
            description: $('#description').val(),
            secciones: secciones
        }
        this.saveLocalStorage(this.resultado);
        console.log(this.resultado)
    }

    get_checkbox(ele, index) {
        ele = $(ele).children()[1];
        ele = $(ele).val();
        const data = {
            valor: index,
            val: ele
        }
        return data;
    }
    get_box(ele, index) {
        ele = $(ele).children()[1];
        ele = $(ele).val();
        const data = {
            valor: index,
            val: ele
        }
        return data;
    }
    get_text(ele, index) {
        ele = $(ele).children()[0];
        console.log(ele)
        ele = $(ele).val();
        const data = {
            valor: index,
            val: ele
        }
        return data;
    }
    get_scale(ele, index) {
        var opciones = []

        var valor1 = $(ele).children()[1];
        var valor10 = $(ele).children()[3];

        valor1 = $(valor1).val();
        valor10 = $(valor10).val();
        var opciones = []
        for (let index = 1; index < 11; index++) {
            opciones.push({
                val: index,
                valor: index
            })
        }
        return opciones;

    }
    saveLocalStorage(objeto) {
        localStorage.setItem('form', JSON.stringify(objeto));
    }
    get_formulario() {
        this.getQuestion();
        return this.resultado;
    }

}