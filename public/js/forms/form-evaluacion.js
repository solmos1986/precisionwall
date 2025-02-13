class Form {
    /**
     * Class constructor will receive the injections as parameters.show-form
     */
    constructor() {
            //console.log('pasa')
        }
        /*event de creacion de campos */
    newSeccion(elemento) {
        var añadir_pregunta = $(elemento).parent().parent().prev();
        console.log(añadir_pregunta)
        $(añadir_pregunta).before(campo);
    }
    newDescription() {
        $("#controls").before(description);
    }
    deleteQuestion(elemento) {
            var padre = $(elemento).parent().parent().parent().parent().parent();
            $(padre).remove()
        }
        /*event de creacion de opciones */
    newOptions(elemento, type) {

        if (type === 'checkBox') {
            $(elemento).parent().parent().prev().append(checkBox);
            
        } else {
            $(elemento).parent().parent().prev().append(box);
        }
        //console.log(esto)
    }
    deleteOption(elemento) {
            //console.log('eliminando')
            $(elemento).parent().remove();
        }
        /* evento de tipo */
    deletePregunta(elemento) {
        console.log('¿pasa')
        console.log(elemento)
        var añadir_pregunta = $(elemento).parent().parent().parent().parent().parent().parent().parent();
        console.log($(añadir_pregunta))
        $(añadir_pregunta).remove();
    }
    updateCheckBox(elemento) {
        //console.log(elemento)
        this.deleteDataOption(elemento);
        var test = $(elemento).parent().parent().parent().next()
        $(test).append(preCheckBox)
       
        this.hideButtonOption(elemento, false);
        //console.log(test)
    }
    updateBox(elemento) {
        //console.log(elemento)
        this.deleteDataOption(elemento);
        var test = $(elemento).parent().parent().parent().next()
        $(test).append(preBox)
       
        this.hideButtonOption(elemento, false);
        //console.log(test)
    }
    updateParagraph(elemento) {
        this.deleteDataOption(elemento);
        $(elemento).parent().parent().parent().next().append(lineas);
        this.hideButtonOption(elemento, true);
        //console.log(elemento)
    }
    updateLinealScale(elemento) {
        this.deleteDataOption(elemento);
        $(elemento).parent().parent().parent().next().append(escalaLineal);
        this.hideButtonOption(elemento, true);
        //console.log(elemento)

    }
    deleteDataOption(elemento) {
        var opcion = $(elemento).parent().parent().parent().next().children().each((index, ele) => {
            $(ele).remove();
        });
        //console.log($(elemento).parent().parent().parent().next().children());
    }
    hideButtonOption(elemento, boolean) {
        var buttonOption = $(elemento).parent().parent().parent().next().next().children()[0];
        //console.log(buttonOption)
        if (boolean) {
            $(buttonOption).hide();
        } else {
            $(buttonOption).show();
        }
    }
}