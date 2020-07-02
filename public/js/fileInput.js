$('#article_file').on('change',function(){
    //get the file name
    var fileName = $(this).val().split("\\");
    //replace the "Choose a file" label
    $(this).next('label').html(fileName[fileName.length-1]);
})