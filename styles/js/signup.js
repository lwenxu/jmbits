/**
 * Created by 童贴鑫 on 2017/4/25.
 */
$(function () {

    var inputs = $(":input:lt(10)");
    var submit = $("#register-submit-btn");
    submit.addClass("disabled");

    var check = function () {
        var missing = 0;
        var i;
        for(i=0; i<4; i++)
        {
            if($(inputs[i]).val()==="")
            {
                missing++;
                //$(inputs[i]).parent().addClass("has-error");
            }
        }
        if(!($(inputs[5]).hasClass("checked")||$(inputs[6]).hasClass("checked")))
        {
            missing++;
        }
        for(i=7; i<10; i++)
        {
            if(!$(inputs[i]).hasClass("checked"))
            {
                missing++;
            }
        }
        if(!missing)
        {
            submit.removeClass("disabled");
        }
        else if(!submit.hasClass("disabled"))
        {
            submit.addClass("disabled");
        }
    };

    var showhidehelp = function (input) {
        var help = $(input).next("span");
        if(help.css("display")==="none")
        {
            help.css("display","inline");
        }
        else
        {
            help.css("display", "none");
        }
    };

    $("input:lt(4)").change(function () {
        check();
    }) ;
    $("input:gt(5):lt(3)").click(function () {
        if($(this).hasClass("checked"))
        {
            $(this).removeClass("checked");
        }
        else
        {
            $(this).addClass("checked");
        }
        check();
    });

    $("input:gt(3):lt(2)").click(function () {
        if(!$(this).hasClass("checked"))
        {
            if($(inputs[5]).hasClass("checked"))
            {
                $(inputs[5]).removeClass("checked");
            }
            if($(inputs[6]).hasClass("checked"))
            {
                $(inputs[6]).removeClass("checked");
            }
            $(this).addClass("checked");
        }
        check();
    });

    $("input:eq(0)").focus(function () {
        showhidehelp(this);
    }).blur(
        function () {
            showhidehelp(this);
        });

    $("input:eq(3)").parent().hover(function () {
        showhidehelp($("input:eq(3)"));
    });
});