<?php

namespace Lampion\Form;

class FormDefaultFields {

    # Input
    const NUMBER  = ['field' => 'input', 'type' => 'number'];
    const INT     = ['field' => 'input', 'type' => 'number'];
    const DATE    = ['field' => 'input', 'type' => 'date'];
    const VARCHAR = ['field' => 'input', 'type' => 'text'];
    const STRING  = ['field' => 'input', 'type' => 'text'];

    # Textarea
    const LONGTEXT   = ['field' => 'textarea'];
    const LONGSTRING = ['field' => 'textarea'];
    const TEXT       = ['field' => 'textarea'];

    # Button
    const BUTTON  = ['field' => 'button', 'type' => 'button'];
    const SUBMIT  = ['field' => 'button', 'type' => 'submit'];

    # Boolean
    const BOOLEAN  = ['field' => 'boolean'];

    # File
    const FILE  = ['field' => 'file'];

    # Nodes
    const NODES  = ['field' => 'title_content'];

}