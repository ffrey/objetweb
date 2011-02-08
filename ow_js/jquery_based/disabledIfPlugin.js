/**
 * @author FFreyssenge
 */
(function( $ ){
/**
 * this function sets all inputs to readOnly (advantage over disabled : the value is
 still passed through $_POST !)
 * the function patches pbs with select/checkbox + IE (surprised ?)
 */
//
  jQuery.fn.disabledIf = function() {
    return this.each(function() {
        var DB = false;
        Elt = $(this);
        //
        if (DB) { alert('elt : ' + this.id + ' / ' + this.type + ' / val : ' + Elt.val() ); }
        switch (this.type) {
            case 'checkbox':
                // if (DB) { alert('case checkbox ! / checked : ' + Elt.attr('checked') ); }
                if (Elt.attr('checked') ) { // pb : when not checked : impossible to know wether it is default (no choice in sirius for this user) or user's choice !
                    // solution : only disable if checked : in that case, it has to be user's choice !
                    Elt.attr('readOnly', true);
                    Elt.change(function(){this.checked = true;});
                }
                break;
            case 'select-one':
                if (DB) { alert('case select ! / selected : ' + Elt.val() ); }
                if ('' != Elt.val() ) {
                    Elt.attr('readOnly', true);
                    Elt.focus( function(){if(DB) { alert('focus'); } this.defaultIndex=this.selectedIndex;});
                    // ! 'change' not taken into account by ie @see http://stackoverflow.com/questions/208471/getting-jquery-to-recognise-change-in-ie
                    Elt.bind($.browser.msie? 'propertychange': 'change', function(){if(DB) { alert('change'); } this.selectedIndex=this.defaultIndex;});
                }
                break;
            default:
                 if ('' != Elt.val() ) {
                    Elt.attr('readOnly', true);
                 }
        }
  })
}
})(jQuery);