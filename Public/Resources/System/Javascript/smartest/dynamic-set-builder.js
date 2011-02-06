Smartest.DynamicSetBuilder = Class.create({
    
    initialize: function(){
        
    },
    
    operator: null,
    aspect: null,
    operatorFirstTime: true,
    valueFirstTime: true,
    
    setAspect: function(givenAspect){
        if($F('new-condition-aspect').charAt(0)){
            $('add-new-condition').value = 'true';
            var aspect = givenAspect ? givenAspect : $F('new-condition-aspect');
            this.aspect = aspect;
            this.buildOperatorField(aspect);
        }else{
            $('add-new-condition').value = 'false';
            $('new-condition-operator-input').update('');
            $('new-condition-value-input').update('');
        }
    },
    
    buildOperatorField: function(aspect){
        var url = sm_domain+'ajax:sets/newConditionOperatorSelect';
        if(this.operatorFirstTime){
            $('operator-loading-text').style.display = 'inline';
            this.operatorFirstTime = false;
        }else{
            $('new-condition-operator-input').update('<span style="color:#999">loading...</span>');
        }
        new Ajax.Updater(
            'new-condition-operator-input',
            url,
            {evalScripts: true, parameters: {aspect: aspect}}
        );
    },
    
    setOperator: function(givenOperator, givenAspect){
        
        $('add-new-condition').value = 'true';
        var aspect = givenAspect ? givenAspect : $F('new-condition-aspect');
        var operator = givenOperator ? givenOperator : $F('new-condition-operator');
        this.operator = operator;
        this.buildValueField(aspect, operator);
        
    },
    
    buildValueField: function(aspect, operator){
        var url = sm_domain+'ajax:sets/newConditionValueSelect';
        
        if($('new-condition-value')){
            var selectedValue = $F('new-condition-value');
        }else{
            var selectedValue = '';
        }
        
        if(this.valueFirstTime){
            $('value-loading-text').style.display = 'inline';
            this.valueFirstTime = false;
        }
        
        new Ajax.Updater(
            'new-condition-value-input',
            url,
            {evalScripts: true, parameters: {aspect: aspect, operator: operator, v: selectedValue}}
        );
    },
    
});