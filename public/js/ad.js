$('#add-image').click(function(){

    // récupérer le n° des futurs champs

    const index = +$('#widget-count').val();

    // récupérer le prototype des entrées

    const tmpl = $('#annonce_images').data('prototype').replace(/__name__/g,index);
    //console.log(tmpl);

    // injecter le code dans la div

    $('#annonce_images').append(tmpl);

    // on ajoute 1 à la valeur initiale de la collection

    $('#widget-count').val(index+1);

    deleteButtons();
});

function updateCounter(){

    const count = +$('#annonce_images div.form-group').length;
    // on met à jour la valeur de widget-counter

    $('#widget-count').val(count);

}

function deleteButtons(){

    $('button[data-action = "delete"]').click(function(){

        // cible toutes les data-target
        const target = this.dataset.target;

        $(target).remove();

    });
}

updateCounter();
deleteButtons();