<?php

class ZkSecurityAdmin extends DataExtension
{

    public function updateEditForm($form){

        $grid = $form->Fields()->dataFieldByName('Members');
        $config = $grid->getConfig();
        $config->removeComponentsByType('GridFieldPaginator');
        $config->removeComponentsByType('GridFieldPageCount');

        $groups = $form->Fields()->dataFieldByName('Groups');
        $groupsConfig = $groups->getConfig();
        $groupsConfig->removeComponentsByType('GridFieldPaginator');
        $groupsConfig->removeComponentsByType('GridFieldPageCount');

    }

}