$(document).ready(function(){$(".tabs-body .tab .checkbox").change(function(e){var s=$(this).is(":checked");$(this).parent("li").find("li").each(function(e,t){$(t).find(".checkbox").prop("checked",s)}),function e(t){var s=!0;$(t).parent("li").parent("ul").find("li").each(function(e,t){$(t).find(".checkbox").is(":checked")||(s=!1)});$(t).parent("li").parent("ul").siblings(".checkbox").prop("checked",s);0!==$(t).parent("li").parent("ul").siblings(".checkbox").parent("li").parent("ul").length&&e($(t).parent("li").parent("ul").siblings(".checkbox"))}(this)});if ($.isFunction($("select.custom").styler)) {$("select.custom").styler()};$(".custom.from").change(function(e){$(this).val();var t=$(this).parent(".jqselect").siblings(".jqselect").find("select");console.log(t)}),$(".custom.to").change(function(e){console.log($(this).val())}),$(".filter-page .more-btn").click(function(){$(this).hasClass("open")?($(this).removeClass("open"),$(this).siblings(".list").find("li").each(function(e,t){2<e&&$(t).slideUp()})):($(this).addClass("open"),$(this).siblings(".list").find("li").slideDown())}),$(".tabs .tabs-header span").click(function(){if(!$(this).hasClass("active")){var e=$(this).attr("data-tab");$(".tabs .tabs-header span").removeClass("active"),$(".tabs .tabs-body .tab").removeClass("active"),$(this).addClass("active"),$("#"+e).addClass("active")}}),$(".tabs .tabs-body .tab .tab-ttl").click(function(){$(this).parent(".tab").hasClass("active")?$(this).parent(".tab").removeClass("active"):($(".tabs .tabs-header span").removeClass("active"),$(".tabs .tabs-body .tab").removeClass("active"),$(this).parent(".tab").addClass("active"))}),$(".tabs .tab .mobile-ttl").click(function(){$(this).siblings("ul").slideToggle(),$(this).toggleClass("active")}),$(".header .mobile-btn").click(function(e){$(this).hasClass("active")?($(this).removeClass("active"),$("#mobile-menu").fadeOut(),$(".header .mobile-btn .close").hide(),$(".header .mobile-btn .open").fadeIn(),$("body, html").attr("style","")):($("body, html").css("overflow","hidden"),$(this).addClass("active"),$("#mobile-menu").fadeIn(),$(".header .mobile-btn .open").hide(),$(".header .mobile-btn .close").fadeIn())})});