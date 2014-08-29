
function load_puzzle() {
  var isMouseDown = false;
  var last_animate = null;
  // {element: null, x: 0, y: 0}
  var selectedElement = null;

  $(".loader").css("display","block");
  $.post("?action=grid", {postID: "'.$postID.'",captchagarb_coupon: jQuery(".captcha_coupon").val()},
              function(d) {
                $(".loader").hide();
                jQuery.globalEval(d);
                
                $(".captcha_grid").children().each(function() {

                  $(this).bind("mousedown", function(e) {
                    isMouseDown = true;

                    e.preventDefault();
                    $(this).css('opacity', '0.5');
                    selectedElement = {};
                    selectedElement.element = this;
                    selectedElement.x = e.pageX;
                    selectedElement.y = e.pageY;
                  });
                  
                  $(this).bind("mouseup",function(e) {
                    isMouseDown = false;

                    var currentElement = $(this); 
                    var currentClass = this.className;
                    var selectedClass = selectedElement.element.className;

                    currentElement.removeClass(currentClass);
                    currentElement.addClass(selectedClass);
                    $(selectedElement.element).removeClass(selectedClass);
                    $(selectedElement.element).addClass(currentClass);

                    $(selectedElement.element).css('opacity', '1');
                    currentElement.css('opacity', '1');
                    selectedElement = null;

                    setCode();
                  });
                  
                  $(this).bind("mouseover mouseout", function (e) {
                    if (isMouseDown === false) return ;
                    if (selectedElement.element == this) return ;
                    
                    switch (e.type) {
                    case 'mouseout':
                      $(this).css({ opacity: '1' });
                      break ;
                    case 'mouseover':
                      $(this).css({ opacity: '0.5' });
                      break ;
                    }
                    return ;
                    var selectedClass = selectedElement.element.className;
                    var currentClass = this.className;

                    if(selectedClass != currentClass) {
                      $(this).css({"cursor":"crosshair"}).stop().animate({"opacity":"0.5"});
                    } 
                  });
                  
                  
                });   
              });
  
  function setCode() {
    var code = [];
    var children = $(".captcha_grid").children();
    for (var i = 0; i < children.length; i++) {
      code.push(children[i].className);
    }
    $(".captcha_code").val(code.join('|'));
  }
  
}

jQuery("document").ready(function() { 
  load_puzzle(); 
  jQuery(".captcha_ver").hide(); 
});
