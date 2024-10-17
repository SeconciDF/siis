<?php
$this->Form->templates([
    'label' => '<label>{{text}}</label>',
    'input' => '<input type="{{type}}" name="{{name}}" {{attrs}} />',
    'formGroup' => '{{label}}<span class="field">{{input}}</span>',
    'inputContainer' => '<p>{{content}}</p>',
    'nestingLabel' => '<label{{attrs}}>{{text}}</label><span class="field">{{hidden}}{{input}}</span>',
    'button' => '<p class="stdformbutton"><button {{attrs}}>{{text}}</button></p>'
]);