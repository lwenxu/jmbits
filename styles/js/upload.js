/**
 * Created by lwen on 2016/12/11.
 */
optional = "可选";
eng_name = "尽量英文名，实在没有英文名的可以考虑拼音。";
year = "如:2014";

media = {
    info: "● BluRay：泛指电影、纪录、剧集蓝光原盘/蓝光原盘压制；\n● DVD：DVD原盘； \n● Remux：BD/DVD视频无损重封装；\n● Encode：重编码，包括BluRay x264/x265、BDRip、DVDRip、HDTVRip等；\n● HDTV：国内外高清电视台采集的源文件；\n● WEB-DL：来源于各种视频网站（如，youtube，爱奇艺）下载的原始片源；",
    options: ["BluRay", "HDTV", "WEB-DL", "Remux", "HDTVRip", "DVD", "DVDRip"],
};

bangumi_media = {
    info: "● TVRip：一般民间字幕组的片选择此介质\n● BDRip：字幕组/压制组由蓝光原盘制作的高质量资源\n● BDMV：常见于U2/ADC发布的无损动漫原盘资源\n● WEB：常见于国内版权商购买新番资源，或者国产动漫\n● DVDRip：字幕组/压制组制作的上古老番资源\n● DVD：常见国漫发售的官方DVD原盘，或是ADC搬运的DVD原盘",
    options: ["TVRip", "BDRip", "WEB", "BDMV", "BDISO", "DVDRip", "DVD"],
};

bangumi_resolution = {
    info: "● 720p：字幕组经常发布的新番分辨率，或是小体积蓝光资源\n● 1080p：大多数动漫BDRip、动漫蓝光原盘的分辨率\n● 2160p：俗称4K，丧心病狂的压制组会做出这种东西\n● 1080i：主要是驻日片源君录制的TV源码（ts）文件\n● 480p：标清，720/640x480 常见于DVDRip\n● 480i：标清，640x480 常见于DVD原盘",
    options: ["720p", "1080p", "2160p", "1080i", "480p", "480i"],
};

bangumi_language = {
    info: "● GB：国标的意思，意为中文简体字幕，Chs即可选GB\n● BIG5：大五码，意为中文繁体字幕，Cht可选BIG5\n● GB\\BIG5：常见于字幕组外挂/内封简繁字幕\n● JP：未经翻译的日文原版生肉",
    options: ["GB", "BIG5", "GB/BIG5", "JP"],
};

bangumi_format = {
    info: "动漫文件的主要格式，MP4、MKV、M2TS（蓝光原盘）等",
    options: ["MP4", "MKV", "M2TS"],
};

bangumi_group = {
    info: "● 填写字幕组/压制组英文名称或资源来源\n● Sumisora：澄空\n● CASO：华盟\n● POPGO：漫游\n● JYFanSub\\JYSub\\KTXP：极影\n● Kamigami：诸神\n● FLsnow：雪飘\n● DMG：动漫国\n● HYSUB：幻樱\n● LKSub：轻国\n● KNA：小夜是个萝莉控\n● Mabors：幻之\n● SHIGURE：时雨初空\n● LittleBakas!：雯雯的胖次好棒！\n● VCB-Studio：啊，框框我的泪！\n● ANK-Raws：智乃的裙下我承包了！\n● ANK-RAWS：求代购美版BD\n● Bilibili：全国最大同性交友网站\n● Youku：据说土豆很好吃\n● iQiYi：业界毒瘤",
    options: ["Sumisora", "JYFanSub", "Kamigami", "DMG", "HYSUB", "KNA", "LKSub", "SHIGURE", "POPGO", "LittleBakas!", "VCB-S", "ANK-Raws", "Bilibili", "iQiYi"],
};

record_source = {
    info: "● 填写播放纪录片的电视台",
    options: ["BBC", "CCTV", "NHK", "BTV", "Discovery", "National.Geographical"],
};

resolution = {
    info: "● 2160p：UHD（Ultra-HD）资源。俗称4K，一般常见4096x2160，3840x2160两种分辨率。\n● 1080p：也称FHD（Full-HD）适用于横向分辨率为1920px的视频资源，21：9的电影资源纵向高度可能不足1080，但仍然视为1080p；\n● 1080i：主要针对HDTV的片源；\n● 720p：HD，常见分辨率为1280x720的视频资源；\n● 480p：标清，720/640x480 常见于DVDRip；\n● 576p：1024x576，常见于早期美剧资源；\n● 480i：标清，640x480 常见于DVD原盘；\n● 其他不规范分辨率请自行按 宽x高 填写",
    options: ["1080p", "720p", "2160p", "1080i", "480p", "576p", "480i"],
};

codec = {
    info: "● x264：适用于BluRay、DVDRip、等大多数Encode类媒介，如果视频格式为AVC，那么即可认为是x264。\n● H264：一般用作WEB-DL资源的描述，是x264的标准规范。\n● x265：H265/HEVC类编码使用此Tag。\n● MPEG2：多见于HDTV电视录制的源码资源。\n● Xvid：Divx规范下的编码器，RMVB/RM/AVI类资源很可能是该格式。\n● WMV：微软主导的视频格式，别名VC-1。",
    options: ["x264", "H264", "x265", "MPEG2", "Xvid", "WMV"],
}

video_format = {
    info: "视频文件后缀名",
    options: ["MP4", "MKV", "TS", "FLV", "RMVB", "AVI"],
};

music_format = {
    info: "音乐资源的格式",
    options: ["FLAC", "WAV", "APE", "TAK", "AAC", "MP3"],
};

muisc_bits = {
    info: "有损音乐资源的码率信息",
    options: ["320Kbps", "192Kbps", "128Kbps"],
};

video_framerate = {
    info: "视频的帧率信息",
    options: ["25fps", "50fps", "30fps", "60fps", "23.976fps", "29.97fps"],
};

format = {
    info: "文件格式",
    options: ["MP4", "MKV", "FLV", "TS", "AVI", "PDF", "ISO", "RAR", "ZIP", "7z"],
};

game_platform = {
    info: "游戏运行平台",
    options: ["Windows", "Linux", "MacOSX", "Android", "iOS", "PSP", "PSV", "PS3", "PS4", "PS2"],
};

game_backup = {
    info: "正版游戏的备份平台",
    options: ["Steam", "Origin", "PlayStation", "Xbox"],
};

game_video = {
    info: "游戏视频的清晰度",
    options: ["超清", "高清", "标清"],
};

game_crack = {
    info: "游戏的破解/打包小组",
    options: ["3DMGAME", "GAMESKY", "RAS", "Reloaded", "CPY"],
};

movie_group = {
    info: "常见压制组",
    options: ["WiKi", "CMCT", "MTeam", "EPiC", "HDChina", "HDS", "beAst", "CtrlHD", "CHD"],
};

drama_group = {
    info: "常见的Scene组",
    options: ["AVS", "KILLERS", "SVA", "TSKS", "DIMENSION", "BATV", "FLEET", "ZiMuZu", "YYeTs", "NTb", "NGB", "DoA", "ZhuixinFan", "Scene"],
};

platform = {
    info: "操作系统平台",
    options: ["Windows", "Linux", "MacOSX", "Android", "iOS", "UNIX"],
};

language = {
    info: "● 简体中文：Chs\n● 英文：En\n● 繁体中文：Cht\n● 日文：JP\n● 多国语言:Multi",
    options: ["Chs", "En", "Cht", "JP", "Multi"],
};

arch = {
    info: "软件若未标注架构，则多为x86架构",
    options: ["x86", "x64", "amd64", "ARM", "MIPS"],
};

all_option_hints = [media, resolution, codec, format, platform, language, arch];

season = "第5季第13集: S05E13，第1集到第20集: E01-E20，如果是第五季全可写S05.Complete，如果是一到五季可写S01-05，单集请写播出时间如20150820";
version = "v3.2, Beta7.1, Alpha2, Ultimate, Professional";
date = "日期格式：2014.06.25";

var rules = {
    401: {
        /* 电影 */
        field: [
            {name: "电影名称", hint: eng_name},
            {name: "上映年份", hint: year},
            {name: "介质", hint: media},
            {name: "分辨率", hint: resolution},
            {name: "编码", hint: codec},
            {name: "制作组", hint: movie_group}
        ],
        category_hint: "请不要发布RMVB格式电影，必然存在质量更好的版本",
        hint: "Dolphin.Tale.2011.BluyRay.720p.x264.DTS-HDChina",
    },
    404: {
        /* 纪录片 */
        field: [{name: "播放电视台", hint: record_source},
            {name: "英文名", hint: eng_name},
            {name: "第几季第几集", hint: season},
            {name: "介质", hint: media},
            {name: "分辨率", hint: resolution},
            {name: "编码", hint: codec},
            {name: "制作组", hint: optional}
        ],
        hint: "A.Bite.of.China.II.S02E03.HDTV.1080i.MPEG2-YYeTs",
        chs: "舌尖上的中国II 2014 CCTV 第三集:时节",
    },
    405: {
        /* 动漫 */
        field: [{name: "罗马音/英文名", hint: "如 Toaru.Kagaku.no.Railgun"},
            {name: "话(卷)数", hint: "03，TV 01-13 Fin，Vol.1-Vol.7"},
            {name: "介质", hint: bangumi_media},
            {name: "语言", hint: bangumi_language},
            {name: "分辨率", hint: bangumi_resolution},
            {name: "格式", hint: bangumi_format},
            {name: "字幕组", hint: bangumi_group}
        ],
        category_hint: "动画，漫画",
        hint: "Toaru.Kagaku.no.Railgun.TV 01-24 Fin+OVA.BDRip.GB.1080p.Sumisora",
    },
    402: {
        /* 剧集 */
        field: [{name: "英文名", hint: eng_name + "如美人心计：Mei.Ren.Xin.Ji"},
            {name: "第几季第几集", hint: season},
            {name: "介质", hint: media},
            {name: "分辨率", hint: resolution},
            {name: "编码", hint: codec},
            {name: "制作组", hint: drama_group}
        ],
        category_hint: "电视剧，美剧，韩剧，英剧等",
        hint: "Mei.Ren.Xin.Ji.E01.HDTV.720p.x264-CnSCG",
    },
    403: {
        /* 综艺 */
        field: [{name: "节目名", hint: eng_name},
            {name: "节目日期", hint: "如，20160823"},
            {name: "介质", hint: media},
            {name: "分辨率", hint: resolution},
            {name: "编码", hint: codec},
            {name: "制作组", hint: optional}
        ],
        hint: "You.Are.The.One.20111204.HDTV.720p.x264-HDCTV",
    },
    406: {
        /* MV */
        field: [{name: "艺术家名", hint: "尽量填写英文名称"},
            {name: "MV名", hint: "尽量以英文名填写"},
            {name: "介质", hint: media},
            {name: "分辨率", hint: resolution},
            {name: "视频编码", hint: codec},
            {name: "视频格式", hint: video_format},
            {name: "制作组", hint: optional}
        ],
        hint: "Avril.Lavigne.Complicated.HDTV.480p.x264.MP4-YYeTs"
    },
    407: {
        /* 体育 */
        field: [
            {name: "类别", hint: "如：世界杯，NBA，世锦赛，法网等"},
            {name: "日期", hint: "如：20140630"},
            {name: "比赛信息", hint: "如：湖人VS小牛"},
            {name: "分辨率", hint: resolution},
            {name: "视频编码", hint: codec},
            {name: "视频帧率", hint: video_framerate},
            {name: "格式", hint: video_format},
            {name: "制作组", hint: optional}
        ],
        hint: "西甲.20150322.巴塞罗那vs皇家马德里.1080p.x264.25fps.MP4-52waha"
    },
    414: {
        /* 音乐 */
        field: [
            {name: "艺术家名，可群星"},
            {name: "专辑名称"},
            {name: "发行年份，如 2016"},
            {name: "码率（无损忽略不填）", hint: muisc_bits},
            {name: "格式", hint: music_format},
            {name: "制作组", hint: optional}
        ],
        hint: "Beethoven.Symphony.No.9.Overture.Coriolan.1963.320Kbps.MP3",
        68: {
            field: [
                {name: "演唱会名称", hint: "尽量填写英文"},
                {name: "公演时间", hint: "，如 2015"},
                {name: "介质", hint: media},
                {name: "分辨率", hint: resolution},
                {name: "视频格式", hint: video_format},
                {name: "制作组", hint: optional}
            ],
            hint: "Hatsune.Miku.Magical.Mirai.in.Nippon.BUDOUKAN.2015.TVRip.720p.MP4-Vmoe"
        }
    },
    408: {
        /* 软件 */
        field: [{name: "软件名称", hint: " Microsoft.Windows, Google.Earth, Adobe.Photoshop"},
            {name: "版本", hint: version},
            {name: "软件语言", hint: language},
            {name: "软件架构", hint: arch},
            {name: "操作系统平台", hint: platform},
        ],
        hint: "Microsoft.Office.2010.With.SP1.Professional.Plus.MSDN.Chs.x86.Windows"
    },
    410: {
        /* PC游戏 */
        field: [
            {name: "游戏名称", hint: eng_name},
            {name: "游戏版本号", hint: version},
            {name: "破解小组(无则留空)", hint: game_crack},
            {name: "游戏语言", hint: language},
            {name: "游戏运行平台", hint: game_platform},
        ],
        hint: "Final.Fantasy.VIII.Chs.Windows",
        14: {
            field: [
                {name: "游戏名", hint: "，如 LOL，DOTA等"},
                {name: "解说", hint: "，如 黑桐谷歌，小智等"},
                {name: "赛事视频名称", hint: "，如 英雄联盟2015全球总决赛"},
                {name: "制作日期", hint: date},
                {name: "清晰度", hint: game_video},
                {name: "格式", hint: video_format},
            ],
            hint: "DOTA.pis解说.PISDOTA高手房单排.海上钢琴师.船长个人秀.2014.06.25.超清.MP4",
        },
        83: {
            field: [
                {name: "游戏名称"},
                {name: "备份时间", hint: "，如 2016.10.01"},
                {name: "正版备份平台", hint: game_backup},
                {name: "游戏语言", hint: language},
                {name: "游戏运行平台", hint: game_platform},
            ],
            hint: "Grand.Theft.Auto.V.2016.10.04.Steam.Cht.Windows",
        },
    },
    411: {
        /* 学习 */
        field: [{name: "资料名称"},
            {name: "来源/作者"},
            {name: "其它", hint: optional},
            {name: "格式", hint: format},
        ],
        hint: "Machine.Learning.Stanford.Open.Course.MP4"
    },
    412: {
        /* 西工大 */
        field: [{name: "此类型较特殊，请按情况用\".\"分隔，自行起格式填写"}],
        hint: ""
    },
    409: {
        field: [{name: "此类型较特殊，请按情况用\".\"分隔，自行起格式填写"}],
        hint: ""
    },
};

function validate_url(url) {
    return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}

function uplist(name, list) {
    sel = $('#' + name);
    sel.empty();
    for (var i in list) {
        val = list[i][0];
        opt = list[i][1];
        sel.append("<option value='" + val + "'>" + opt + "</option>");
    }
    sel.selectpicker('refresh');
}

function field_change() {
    if ($("#torrent_name_checkbox").prop("checked")) {
        return;
    }

    id = $("#browsecat").val();
    rule = rules[id];

    var field = [];
    var is_group = false;
    var last = -1;
    for (var i = 0; i < rule.field.length; i++) {
        /* remove prefix/trailing spaces */
        f = $.trim($('#torrent_name_field' + i).val());

        /* replace spaces to dots */
        f = f.replace(/\s+/g, '.');

        /* remove prefix/trailing dots */
        f = f.replace(/^\.+/, '').replace(/\.+$/, '');

        if (f) {
            field.push(f);
        }

        if ($('#torrent_name_field' + i).attr("placeholder") == "制作组") {
            is_group = true;
        }
        last++;
    }

    title = field.join(".");


    if (is_group) {
        group = $('#torrent_name_field' + last).val();
        if (group) {
            pattern = '.' + group
            title = title.replace(pattern, '-' + group);
        }
    }

    $("#name").val(title);
}

function fill_field(i, option) {
    fid = "torrent_name_field" + i;
    $('#' + fid).val(option);
    field_change();
}

/* fill in hints, generate inputs according to rule */
function fill_in_rule(rule) {
    /* generate title inputs */
    tn = $("#torrent_name");
    tn.empty();
    for (var i = 0; i < rule.field.length; i++) {
        if (!rule.field[i]) continue;
        w = 100.0 / rule.field.length;
        fid = "torrent_name_field" + i;

        hint = rule.field[i].hint;
        name = rule.field[i].name;
        if (hint && hint.options) {
            bid = "torrent_option_btn" + i;
            /* show option selects */
            opts = '<td width="' + w + '%"><div class="input-group">';
            opts += "<input type='text' class='form-control' id='" + fid + "'" + "placeholder='" + name + "' title='" + hint.info + "' autocomplete='off' />";
            opts += '<div class="input-group-btn">';
            opts += '<button type="button" id="' + bid + '" class="btn btn-default dropdown-toggle dropdown-thin" data-toggle="dropdown">';
            opts += '&nbsp;<span class="caret"></span>';
            opts += '</button>';
            opts += '<ul class="dropdown-menu dropdown-menu-right" role="menu">';

            for (var j = 0; j < hint.options.length; j++) {
                opts += "<li><a href=\"javascript:fill_field(" + i + ",'" + hint.options[j] + "');\">" + hint.options[j] + "</a></li>";
            }
            opts += '</ul> </div> </div></td>';
            tn.append(opts);

            $('#' + fid).click($('#' + bid), function (event) {
                event.stopPropagation();
                event.data.dropdown("toggle");
                $(this).focus();
            });
        } else {
            if (!hint) {
                ht = "";
            } else {
                ht = hint;
            }
            /* show input box */
            title = name + ht;
            tn.append("<td width='" + w + "%'><input type='text' class='form-control' id='" + fid + "'" + "placeholder='" + name + "' title='" + title + "' /></td>");
            $('#' + fid).tooltip({'trigger': 'focus', 'placement': 'bottom'});
        }

        input = $('#' + fid);
        input.bind("change", input, field_change);
        input.bind("keyup", input, field_change);
    }

    /* only for IE <= 8 */
    if (!!$.prototype.placeholder) {
        $('input, textarea').placeholder();
    }
}

function category_change(event) {
    id = $("#browsecat").val();
    if (id == "0") return;

    secondtype(id);

    rule = rules[id];

    fill_in_rule(rule);

    show_category_rules(id);

    question_dialog(id);
}

function subcategory_change(event) {
    id = $("#browsecat").val();
    sub_id = $("#source_sel").val();
    if (id == "0" || sub_id == "0") return;

    rule = rules[id];

    if (rule[sub_id]) {
        rule = rule[sub_id];
        fill_in_rule(rule);
    }

}

function is_in_keys(v, keys) {
    function format(s) {
        return s.replace(/\.|\-/g, '').toLowerCase();
    }

    for (var i in keys) {
        if (format(keys[i]).search(format(v)) != -1) {
            return true;
        }
    }
    return false;
}

/* try the best to fill in title sections */
function guess_suggests(keys) {
    id = $("#browsecat").val();
    sub_id = $("#source_sel").val();
    if (id == "0" || sub_id == "0") return;

    rule = rules[id];

    if (rule[sub_id]) {
        rule = rule[sub_id];
    }

    for (var i = 0; i < rule.field.length; i++) {
        var tnf = $('#torrent_name_field' + i);
        tnf.val('');
        for (var j in all_option_hints) {
            var hint = all_option_hints[j];
            if (rule.field[i].hint === hint) {
                for (var k in hint.options) {
                    opt = hint.options[k];
                    if (is_in_keys(opt, keys)) {
                        tnf.val(opt);
                    }
                }
            }
        }
    }
    field_change();
}

function trim_width(str, len) {
    if (str.length <= len) {
        return str;
    }
    return str.substr(0, len) + "..";
}
function checkdupe(src) {
    $.ajax({
        type: "GET",
        url: "checkdupe.php",
        dataType: "html",
        data: {name: $('#name').val(), small_descr: $('#small_descr').val()},
        success: function (html) {
            $('#checkdupe_modal_content').html(html);

            if (src == 'checkdupe_btn') {
                $('#checkdupe_modal_publish_btn').hide();
            } else {
                $('#checkdupe_modal_publish_btn').show();
            }
            name_small_descr = $('#name').val() + " - " + $('#small_descr').val();
            $('#checkdupe_modal_title').html(trim_width(name_small_descr, 100));
            $('#checkdupe_modal').modal('show');
            timer = setInterval(function () {
                $('#checkdupe_modal_content').slimScroll({scrollTo: '0'});
                clearInterval(timer)
            }, 500);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#checkdupe_modal_content').html("<span class='red'>查重出错：" + errorThrown + "</span>");
        },
    });
}

$(document).ready(function () {

    $("#torrent_name_checkbox").change(function () {
        if ($(this).prop("checked") == true) {
            $("#name").attr("disabled", false);
        } else {
            $("#name").attr("disabled", true);
        }
    });

    uplist("source_sel", new Array(['0', '请先选择一级类型']));

    $("#torrent_file").change(function () {
        name = $(this).val().replace(/.*(\/|\\)/, '').replace(/\.torrent/g, '');
        raw_keys = name.replace(/[\.\[\]]/g, ',').replace(/\s+/g, ',').split(',');
        keys = [];
        for (var i in raw_keys) {
            k = raw_keys[i];
            if (k) {
                keys.push(k);
            }
        }
        $("#torrent_file_name").html("建议：" + keys.join("."));
        guess_suggests(keys);
        $("transferred_torrent_file_name").empty();
        $("transferred_torrent_file_base64").empty();
    });

    $("#browsecat").bind("mouseup", category_change);
    $("#browsecat").bind("change", category_change);

    $("#source_sel").bind("mouseup", subcategory_change);
    $("#source_sel").bind("change", subcategory_change);

    $("#qr").click(function () {
        var err = "";
        if ($("#browsecat").val() == 0) err += "<p>请选择 <font color='red'>类型</font></p>";
        if ($("#source_sel").val() == 0) err += "<p>请选择 <font color='red'>子类型</font></p>";
        if ($("#torrent_file").val() == "" && $("#transferred_torrent_file_base64").val() == "") {
            err += "<p>请选择 <font color='red'>种子文件</font></p>";
        }
        if ($("#name").val().length < 10) err += "<p><font color='red'>标题</font>内容不得少于10个字符</p>";
        //if($("#descr").val().length < 50) err += "[简介]内容不得少于50个字符\n\n";
        category = $("#browsecat").val();
        source = $("#source_sel").val();
        /* special restriction for movies */
        descr = $("#descr").val();

        /**
         * 电影/剧集/纪录片 必须填写 IMDb或豆瓣号
         */
        if ($.inArray(category, ['401', '402', '404']) != -1) {
            if (descr.search(/(tt[0-9]{7})/) == -1 && descr.search(/movie\.douban\.com\/subject\/[0-9]+/) == -1) {
                err += "<p><b>电影/剧集/动画/纪录片</b> 类资源必须在简介中包含 <font color='red'><b>IMDb</b></font> 或 <font color='red'><b>豆瓣</b></font> 链接，类似如下：";
                err += "<ul>";
                err += " <li>IMDb链接: http://www.imdb.com/title/[tt数字编号]/ </li>";
                err += " <li>豆瓣链接: http://movie.douban.com/subject/[数字编号]/ </li>";
                err += "</ul>";
                err += "关于IMDb链接<ul>";
                err += " <li><a href='http://zh.wikipedia.org/zh-cn/imdb' target='_blank'>互联网电影数据库（Internet Movie Database，IMDb）</a>";
                err += "   是一个关于电影演员、电影、电视节目、电视艺人、电子游戏和电影制作小组的在线数据库";
                err += " </li>";
                err += " <li>IMDb链接请到";
                err += "   <a class='btn btn-warning btn-sm' href='http://www.imdb.com' target='_blank' title='点击打开新的窗口'>";
                err += "IMDb网站";
                err += " <span class='glyphicon glyphicon-new-window'></span>";
                err += "</a>";
                err += " 查询";
                err += "</li>";
                err += "</ul>";
                err += "关于豆瓣链接<ul>";
                err += " <li>豆瓣链接请到 ";
                err += "  <a class='btn btn-success btn-sm' href='http://movie.douban.com' target='_blank' title='点击打开新的窗口'>";
                err += "豆瓣网站";
                err += " <span class='glyphicon glyphicon-new-window'></span>";
                err += "</a>";
                err += " 查询</li>";
                err += "</ul>";
                err += " 该规定是为了方便用户检索和查重(种子列表中的'其它版本'链接就是根据IMDb/豆瓣号确定的)，使资源更有序并且有利于保种，请您理解。";
                err += " 如果查不到IMDb或豆瓣号（如网络视频、微电影等），请发到<b>其它</b>分类</p>";
            }
        }
        /**
         * 动画 必须填写 Bangumi链接
         */
        if (source == 45) {
            if (descr.search(/(bangumi\.tv|bgm\.tv)\/subject\/[0-9]+/) == -1) {
                err += "<p><b>动画</b> 类资源必须在简介中包含 <font color='red'><b>Bangumi</b></font> 链接，类似如下：";
                err += "<ul>";
                err += " <li>Bangumi链接: http://bangumi.tv/subject/[数字编号] </li>";
                err += "</ul>";
                err += "关于Bangumi链接<ul>";
                err += " <li> <a href='http://bangumi.tv'>Bangumi</a>";
                err += "    是由 Sai 于桂林发起的 ACG 分享与交流项目，致力于让阿宅们在欣赏ACG作品之余拥有一个轻松便捷独特的交流与沟通环境。";
                err += " </li>";
                err += " <li>Bangumi链接请到";
                err += "   <a class='btn btn-warning btn-sm' href='http://bangumi.tv' target='_blank' title='点击打开新的窗口'>";
                err += "Bangumi网站";
                err += " <span class='glyphicon glyphicon-new-window'></span>";
                err += "</a>";
                err += " 查询";
                err += "</li>";
                err += " 该规定是为了方便用户检索和查重(种子列表中的'其它版本'链接就是根据IMDb/豆瓣号/Bangumi确定的)，使资源更有序并且有利于保种，请您理解。";
                err += " 如果查不到Bangumi链接（如网络动画、自制动画等），请发到<b>其它</b>分类</p>";
            }
        }

        /* at least one image */
        if (descr.search(/\[img\]([^\<\r\n\"']+?)\[\/img\]/) == -1 && descr.search(/\[attach\]([^\<\r\n\"']+?)\[\/attach\]/) == -1) {
            err += "<p>请在<b>简介</b>中提供至少一张<font color='red'>与资源有关的图片</font>作为封面。</p>";
        }

        if (err == "") {
            $("#name").attr("disabled", false);
            checkdupe('qr');
            return;
        }
        bootbox.alert("<h4><span class='glyphicon glyphicon-exclamation-sign'></span> 部分区域未填写完整</h4>" + err);
    });

    $('#checkdupe_modal_publish_btn').click(function () {
        $(this).button('loading')

        name = $("#name").val();
        small_descr = $("#small_descr").val();
        descr = $("#descr").val();
        transferred_url = $("#transferred_url").val();

        $("#name").val(base64_encode(name));
        $("#small_descr").val(base64_encode(small_descr));
        $("#descr").val(base64_encode(descr));
        $("#transferred_url").val(base64_encode(transferred_url));

        $("#compose").submit();

        /* back to original form text */
        $("#name").val(name);
        $("#small_descr").val(small_descr);
        $("#descr").val(descr);
        $("#transferred_url").val(transferred_url);
    });

    $('#transfer_btn').click(function (event) {

        event.preventDefault();

        if (!validate_url($('#transferred_url').val())) {
            $('#transferred_url_validate_modal').modal('show')
            return;
        }

        progress_bar = '<div class="progress">' +
            '<div id="transfer_progress"' +
            'class="progress-bar progress-bar-success progress-bar-striped active"' +
            'role="progressbar" aria-valuenow="0" aria-valuemin="0"' +
            'aria-valuemax="100" style="width: 0%;">' +
            '</div></div>';
        $('#torrent_file_name').html(progress_bar);

        function progress_get() {
            p = $('#transfer_progress')
            return parseInt(p.attr("aria-valuenow"));
        }

        function progress_set(count) {
            p = $('#transfer_progress')
            p.css("width", count + '%');
            p.attr("aria-valuenow", count);
            p.html(count + '%');
        }

        function progress() {
            count = progress_get()
            if (count >= 99) {
                clearInterval(timer)
            } else {
                progress_set(count + 1);
            }
        }

        timer = setInterval(progress, 250);

        $.ajax({
            type: "GET",
            url: "transfer.php",
            dataType: "json",
            data: {url: base64_encode($('#transferred_url').val())},
            success: function (json) {
                clearInterval(timer)

                $('#name').val($.trim(json.name));
                $("#name").attr("disabled", false);
                $('#torrent_name_checkbox').prop("checked", true);

                $('#small_descr').val(json.small_descr);
                $('#descr').val(json.descr);

                /* temporarily unbind category_change don't show ruled inputs */
                $("#browsecat").unbind("change", category_change);
                $('#browsecat').selectpicker('val', json.category);
                secondtype(json.category.toString());
                $("#browsecat").bind("change", category_change);
                show_category_rules(json.category);

                $('#source_sel').selectpicker('val', json.sub_category);

                if (json.torrent_file_name.length > 0) {
                    $('#torrent_file').hide();
                    $('#transferred_torrent_file_name').val(json.torrent_file_name);
                    $('#transferred_torrent_file_base64').val(json.torrent);
                    fname = "<span class='label label-default'>" + json.torrent_file_name + "</span>";
                    $('#torrent_file_name').html(fname);
                } else {
                    $('#torrent_file_name').empty();
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('#torrent_file_name').html("<span class='red'>载入种子信息出错：" + errorThrown + "</span>");
            },
        });
    });

    $('#upload_reset').click(function () {
        if (confirm("确定重置？所有内容将被清空！")) {
            /* reset torrent file button */
            $('#torrent_file').replaceWith($('#torrent_file').clone(true));
            $('#torrent_file').show();
            $('#torrent_name').empty();
            $('#torrent_file_name').empty();
        }
    });
    $('#checkdupe_btn').click(function (event) {
        checkdupe('checkdupe_btn');
    });

});

var questions, question_index;

function load_question(question) {
    $('#question_progress').html("第" + (question_index + 1) + "题 / 共" + questions.length + "题");
    $('#question_title').html(question.title);
    $('#option1_text').html(question.option1);
    $('#option2_text').html(question.option2);
    $('#option3_text').html(question.option3);
    $('#option4_text').html(question.option4);
    for (i = 1; i <= 4; i++) {
        $('#option' + i).prop("checked", false);
        $('#option' + i + "_check").html('');
    }
    $('#question_next').prop('disabled', true);
}

function question_dialog(catid) {
    if ($('#question_modal').length == 0) {
        return;
    }

    question_index = 0;
    questions = null;
    $('#question_next').show();
    $('#question_next').prop('disabled', true);
    $('#question_close').hide();

    $.get('api.php', {'type': 'questions', 'subtype': 'category', 'catid': catid}, function (data) {
        questions = data.questions;

        if (questions && questions.length > 0) {
            $('#question_modal').modal('show');
            question_index = 0;
            load_question(questions[question_index]);
        }
    });

    $('#question_submit').click(function () {
        var optsel;
        for (i = 1; i <= 4; i++) {
            if ($('#option' + i).prop("checked")) {
                optsel = i;
            }
        }

        if (questions && questions.length > 0) {
            qid = questions[question_index].id;
            $.get('api.php', {'type': 'questions', 'subtype': 'answer', 'id': qid, 'option': optsel}, function (data) {
                if (data.id == qid && data.result == 'correct') {
                    $('#option' + optsel + '_check').html("<span class='glyphicon glyphicon-ok green'></span>");
                    $('#question_next').prop('disabled', false);

                    /* question finished */
                    if (question_index == questions.length - 1) {
                        $('#question_next').hide();
                        $('#question_close').show();

                        $.get('api.php', {'type': 'questions', 'subtype': 'finish', 'catid': catid});

                        questions = null;
                        question_index = 0;
                    }
                } else {
                    $('#option' + optsel + '_check').html("<span class='glyphicon glyphicon-remove red'></span>");
                }
            });
        }
    });

    $('#question_next').click(function () {
        if (questions && questions.length > 0) {
            if (question_index < questions.length - 1) {
                question_index += 1;
                load_question(questions[question_index]);
            }
        }
    });
}

