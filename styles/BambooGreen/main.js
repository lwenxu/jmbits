/**
 * Created by xpf19 on 2017/2/7.
 */

$(function () {
    $('#torrent_tooltip').tooltip({
        trigger: 'foucs'
    });
    $('#torrent_title_tooltip').tooltip({
        trigger: 'foucs'
    });
    $('#name').tooltip({
        trigger: 'focus'
    });
    $('#small_name').tooltip({
        trigger: 'focus'
    });
    $('#piclink').tooltip({
        trigger: 'focus'
    });
    $('#browsecat').change(function () {
        var id=$('#browsecat').val();
        $.post('secondcategory.php',
                                    {
                                        cate_id:id
                                    },
                                function (response,status,xhr) {
                                    // alert(response);
                                    $('#secondcategory').children().remove();
                                    var arr=response.split(',');
                                    var str='';
                                    for (var i=0;i<arr.length-1;i+=2){
                                        str+='<option value='+arr[i]+'>'+arr[i+1]+'</option>';
                                    }
                                    var html='<select class="form-control input-large" name="secondid">'+str+'</select>';
                                    $('#secondcategory').append(html);
                                    if (id==0){
                                        $('#secondcategory').children().remove();
                                    }
                                })
    });
    $('#oricat').change(function () {
        var id = $('#oricat').val();
        $.post('secondcategory.php',
            {
                cate_id: id
            },
            function (response, status, xhr) {
                // alert(response);
                $('#secondcategoryedit').children().remove();
                var arr = response.split(',');
                var str = '';
                for (var i = 0; i < arr.length - 1; i += 2) {
                    str += '<option value=' + arr[i] + '>' + arr[i + 1] + '</option>';
                }
                var html = '<select class="form-control input-large" name="secondid">' + str + '</select>';
                $('#secondcategoryedit').append(html);
                if (id == 0) {
                    $('#secondcategoryedit').children().remove();
                }
            })
    });
    $('#comment_short_text').val($('#comment_short_select').val());
    $('#comment_short_select').change(function () {
        // alert($('#comment_short_select').val());
        $('#comment_short_text').val("");
        $('#comment_short_text').val($('#comment_short_select').val());
    });

});