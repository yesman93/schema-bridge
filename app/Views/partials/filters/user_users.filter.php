<?php

use Lumio\View\Components;
use Lumio\View\Components\Form;




$form = $form ?? Form::build();


$source = [];
$source[] = ['value' => 1, 'label' => __tx('Administrator')];
$source[] = ['value' => 2, 'label' => __tx('User')];
$source[] = ['value' => 3, 'label' => __tx('Guest')];



$form->select(new \Lumio\DTO\View\FormInput(
    name: 'role_id',
    label: __tx('Role'),
    source: $source,
));






