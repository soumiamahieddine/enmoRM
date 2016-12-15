/* ========================================================================
 * DataForm: dataForm.js v0.0.1a
 * ========================================================================
 * Copyright 2014 VEBER Arnaud
 * Licensed under GNU General Public License
 * ======================================================================== */

(function($) {
    $.dataForm = function(element) {
        var plugin       = this;
        var element      = element;
        var $element     = $(element);
        var dfSerialized = {};

        plugin.init = function()
        {
        }
        plugin.init();

        plugin.serialize = function()
        {
            _make();
            return dfSerialized;
        }

        var _make = function()
        {
            var rootNs           = _getRootNs();
            dfSerialized[rootNs] = _getSerializedNs(rootNs);

            _forward(rootNs, dfSerialized[rootNs]);
        }

        var _forward = function(ns, node)
        {
            $(_getChildrenNsForNs(ns)).each(function() {
                node[this] = _getSerializedNs(this);

                _forward(this, node[this]);
            });
        }

        /**
         * _getRootNs [OK]
         */
        var _getRootNs = function()
        {
            if ($element.attr('data-form-ns').length > 0)
                return $element.attr('data-form-ns');

            else if ($element.find('[data-form-ns]:first').attr('data-form-ns').length > 0)
                return $element.find('[data-form-ns]:first').attr('data-form-ns');

            else
                return false;
        }

        var _getChildrenNsForNs = function(ns)
        {
            var $childElement, closestNs, childrenNs = [];

            $(_getElementWithNs(ns)).find('[data-form-ns]').each(function() {
                $childElement = $(this);

                closestNs = $childElement.parent().closest('[data-form-ns]').attr('data-form-ns');
                if (closestNs == ns)
                    childrenNs.push($childElement.attr('data-form-ns'));

            });

            return childrenNs;
        }

        var _getElementWithNs = function(ns)
        {
            return $element.parent().find('[data-form-ns="' + ns + '"]');
        }

        var _getSerializedNs = function(ns)
        {
            var nsForms = _getFormsForNs(ns);

            if (_isAToManyRelation(ns))
                return _serializeFormsToMany(nsForms);

            else
                return _serializeFormsToOne(nsForms);
        }

        var _serializeFormsToOne = function(forms)
        {
            var o = {};
            $(forms).each(function(){
                var $form = $(this);
                $($form.serializeArray()).each(function(){
                    var formControl = this;
                    o[formControl.name] = formControl.value;
                });
            });
            return o;
        }

        var _serializeFormsToMany = function(forms)
        {
            var a = [];
            $(forms).each(function(){
                var $form = $(this), o = {};
                $($form.serializeArray()).each(function() {
                    var formControl = (this);
                    o[formControl.name] = formControl.value;
                })
                a.push(o);
            });
            return a;
        }

        var _getFormsForNs = function(ns)
        {
            var $form, closestNs, nsForms = [];

            $(_getElementWithNs(ns).parent().find('form')).each(function() {
                $form = $(this);

                closestNs = $form.closest('[data-form-ns]').attr('data-form-ns');
                if (closestNs == ns) {
                    nsForms.push($form);
                }
            });

            return nsForms;
        }

        var _isAToManyRelation = function(ns)
        {
            return (_getElementWithNs(ns).attr('data-form-rel') == 'toMany');
        }
    }

    $.fn.dataForm = function() {
        return this.each(function() {
            if (undefined == $(this).data('dataForm')) {
                var plugin = new $.dataForm(this);
                $(this).data('dataForm', plugin);
            }
        });
    }
})(jQuery);
