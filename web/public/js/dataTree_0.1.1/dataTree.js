(function($)
{
  $.fn.dataTree=function(options)
  {
    /*
     * Settings
     */
    var settings = $.extend({
      iconBranchClose: "fa fa-plus-sign-alt",
      iconBranchOpen: "fa fa-minus-sign-alt",
      iconLeaf: "fa fa-file-alt",
      toggleEasing: "easeOutElastic",
      toggleDuration: 1000,
      expanded: false
    }, options );

    return this.each(function()
    {
      /*
       * Load
       */
      var $this = $(this);

      $this.find('.dropdown').css('visibility', 'hidden');

      $this.find('ul').not(".dropdown-menu").each(function(){
        var $this = $(this);
        if (settings.expanded) {
          $this.addClass('datatree-branch-open').removeClass('datatree-branch-close').css('display', 'block');
        } else {
          $this.addClass('datatree-branch-close').removeClass('datatree-branch-open').css('display', 'none');
        }
      });

      $this.find('li').each(function(){
        var $this = $(this);

        if ($this.parentsUntil('.dropdown-menu').length <= 1) return true;

        var $icon = $($this.find('.datatree-icon')[0]);

        if ($this.find('ul').not(".dropdown-menu").length > 0) {
          $this.addClass('datatree-branch');
          if ($this.find('ul').not(".dropdown-menu").css('display') == 'none') {
            $icon.addClass(settings.iconBranchClose);
          } else {
            $icon.addClass(settings.iconBranchOpen);
          }
        } else {
          $this.addClass('datatree-leaf');
          $icon.addClass(settings.iconLeaf);
        }
      });

      /*
       * Event
       */
      // show dropdown button on mouse over
      $this.find('.dropdown').closest('div').mouseover(function() {
        var $this = $(this);
        var $button = $($this.find('.dropdown')[0]);
        $button.css('visibility', 'visible');
      });

      // hide dropdown button on mouse out
      $this.find('.dropdown').closest('div').mouseout(function() {
        var $this = $(this);
        var $button = $($this.find('.dropdown')[0]);
        $button.css('visibility', 'hidden');
      });

      // Open & Close datatree-branch or alert content of datatree-leaf on click datatree-opener
      $this.find('.datatree-opener').click(function(event){
        event.preventDefault();
        var $this = $(this);
        var $li = $($this.closest('li'));

        if ($li.hasClass('datatree-branch')) {
          var $ul = $($li.find('ul').not(".dropdown-menu")[0]);
          var $icon = $($li.find('.datatree-icon')[0]);
          $ul.stop().slideToggle(settings.toggleDuration, settings.toggleEasing, function(){
            if ($ul.css('display') == 'none') {
              $icon.removeClass(settings.iconBranchOpen).addClass(settings.iconBranchClose);
            } else {
              $icon.removeClass(settings.iconBranchClose).addClass(settings.iconBranchOpen);
            }
          });
        } else {
          alert($li.find('.datatree-opener').text());
        }
      });

      // set pointer cursor on mouse over datatree-opener
      $this.find('.datatree-opener').mouseover(function(){
        $(this).css('cursor', 'pointer');
      });
    });
  };
})(jQuery);