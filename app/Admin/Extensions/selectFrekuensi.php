<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class selectFrekuensi
{
    protected $id;

    public function __construct($id , $aktif)
    {
        $this->id = $id;
        $this->aktif = $aktif;
    }

    protected function script()
    {
        return <<<SCRIPT

$('.grid-check-row').on('click', function () {

    // Your code.
    console.log($(this).data('aktif'));

    var Id = $(this).data('id');

    $.ajax({
                url: '/admin/update/ketersediaan/' + Id,
                type: 'GET',
                success: function(response)
                {
                    console.log('Sukses');
                }
            });
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        // return "<a class='btn btn-xs btn-success fa fa-check grid-check-row' data-id='{$this->id}'></a>";

        if($this->aktif == 1)
        {
            return "<input type='checkbox' class='grid-check-row'  data-id='{$this->id}' checked/>";    
        }
        else
        {
            return "<input type='checkbox' class='grid-check-row'  data-id='{$this->id}'/>";       
        }
        
    }

    public function __toString()
    {
        return $this->render();
    }
}