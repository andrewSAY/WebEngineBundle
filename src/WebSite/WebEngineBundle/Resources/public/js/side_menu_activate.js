/**
 * Created by Сапрыкин А_Ю on 17.09.15.
 */
function side_menu_activate()
{
    $('.sb_menu li').mouseenter(function(){
        $(this).stop().animate({"padding-left":"15px"},150);
    }).mouseleave(function(){
        $(this).stop().animate({"padding-left":"0px"},150);
    });
}