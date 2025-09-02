<?php






$form = \Lumio\View\Components\Form::build();

//$form = \Lumio\View\Components\Form::build(new \Lumio\DTO\View\FormSetup(
//    is_card: false,
//    show_title: false,
//));



$form->text(new \Lumio\DTO\View\FormInput(
    name: 'test',
    label: 'Test',
));

$form->text(new \Lumio\DTO\View\FormInput(
    name: 'test2',
    label: 'Test 2',
));

$form->text(new \Lumio\DTO\View\FormInput(
    name: 'test3',
    label: 'Test 3',
));

$form->text(new \Lumio\DTO\View\FormInput(
    name: 'test4',
    label: 'Test 4',
));

$form->submit();



