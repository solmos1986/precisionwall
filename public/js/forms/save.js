class Save {

    constructor() {

    }
    inizialize() {
        let seccion=[]
        $('#contenedor').children().each((index, ele) => {
            //console.log(ele);
            
            if (index !== 0 && index !== 1 && index !== (total - 1)) {
                //console.log(ele); //obteniedo el contenido seccion

                let preguntas=[]
                var datos = $(ele).children().children().children().each((index, elem)=>{
                    
                    if (index !== 0 && index !== 1){
                        //
                        var option_box = $(elem).hasClass('box');
                        var option_checkbox = $(elem).hasClass('checkbox');
                        var option_text = $(elem).hasClass('text');
                        var option_scale = $(elem).hasClass('scale');

                        //call methods
                        if (option_box) {
                            //tipo = "box";
                            const res = this.rastrearBox(elem);
                            preguntas.push({
                                form_pregunta_id:$(elem).attr("data-id"),
                                respuestas:res
                            });
                        }
                        if (option_checkbox) {
                            //tipo = "checkbox";
                            const res = this.rastrearCheckBox(elem);
                            preguntas.push({
                                form_pregunta_id:$(elem).attr("data-id"),
                                respuestas:res
                            });
                        }
                        if (option_text) {
                            //tipo = "text";
                            const res = this.rastrearText(elem);
                            preguntas.push({
                                form_pregunta_id:$(elem).attr("data-id"),
                                respuestas:res
                            });
                        }
                        if (option_scale) {
                            //tipo = "scale";
                            const res = this.rastrearScale(elem);
                            preguntas.push({
                                form_pregunta_id:$(elem).attr("data-id"),
                                respuestas:res
                            });
                        }
                    }
                   
                });
                seccion.push({
                    seccion:$(ele).attr("data-seccion"),
                    id_seccion:$(ele).attr("data-id"),
                    preguntas:preguntas
                })
                
            }
        });
        let formulario={
            Empleado_ID:$('#Empleado_ID').val(),
            evaluacion_id:$('#evaluacion_id').val(),
            formulario_id:$('#formulario_id').val(),
            personal_evaluaciones_id:$('#personal_evaluaciones_id').val(),
            secciones:seccion
        };
        console.log(formulario);
        return formulario;
    }
    rastrearBox(elem){
        let check=[];
        var elemento=$(elem).children()[1];
        elemento=$(elemento).children()[0];
        $(elemento).children().each((index,ele)=>{
            var box=$(ele).children()[0];
            box=$(box).children()[0];
            if ($(box).prop('checked')) {
                check.push(
                    {
                        form_respuesta_id:$(box).prop('value'),
                        val:""
                    }
                );
            }
        })
        return check
    }
    rastrearCheckBox(elem){
        let check=[];
        var elemento=$(elem).children()[1];
        elemento=$(elemento).children()[0];
        $(elemento).children().each((index,ele)=>{
            var checkbox=$(ele).children()[0];
            checkbox=$(checkbox).children()[0];
            if ($(checkbox).prop('checked')) {
                check.push(
                    {
                        form_respuesta_id:$(checkbox).prop('value'),
                        val:""
                    }
                );
            }
        })
        return check
    }
    rastrearText(elem){
        let check=[];
        var elemento=$(elem).children()[0];
        elemento=$(elemento).children()[0];
        elemento=$(elemento).children()[0];
        elemento=$(elemento).next();
        check.push(
            {
                form_respuesta_id:$(elemento).prop('name'),
                val:$(elemento).prop('value')
            }
        );
        return check
    }
    rastrearScale(elem){
        let check=[];
        var elemento=$(elem).children()[1];
        elemento=$(elemento).children()[0];
        $(elemento).children().each((index,ele)=>{
            if (index!==0&&index!==11) {
                var scale=$(ele).children()[0];
                scale=$(scale).children()[0];
                if ($(scale).prop('checked')) {
                    check.push(
                        {
                            form_respuesta_id:$(scale).prop('value'),
                            val:""
                        }
                    );
                }
            }
        })
        return check
    }
}