<?php

//permissions.php
//Для require

    if (isset($_SESSION['permissions'])) {
        if ($_SESSION['permissions'] == '777') {
            $god_mode = TRUE;
        } else {
            //Получили список прав
            $permissions = SelDataFromDB('spr_permissions', $_SESSION['permissions'], 'id');
            //var_dump($permissions);
        }
        if (!$god_mode) {
            if ($permissions != 0) {
                /*foreach ($permissions[0] as $name => $data){
                    var_dump($name);
                }*/

                $clients = json_decode($permissions[0]['clients'], true);
                $groups = json_decode($permissions[0]['groups'], true);
                $workers = json_decode($permissions[0]['workers'], true);
                $offices = json_decode($permissions[0]['offices'], true);
                $scheduler = json_decode($permissions[0]['scheduler'], true);
                $finance = json_decode($permissions[0]['finance'], true);
            }
        } else {
            //Видимость
            $clients['see_all'] = 0;
            $clients['see_own'] = 0;
            $groups['see_all'] = 0;
            $groups['see_own'] = 0;
            $workers['see_all'] = 0;
            $workers['see_own'] = 0;
            $offices['see_all'] = 0;
            $offices['see_own'] = 0;
            $scheduler['see_all'] = 0;
            $scheduler['see_own'] = 0;
            $finance['see_all'] = 0;
            $finance['see_own'] = 0;
            //
            $clients['add_new'] = 0;
            $clients['add_own'] = 0;
            $groups['add_new'] = 0;
            $groups['add_own'] = 0;
            $workers['add_new'] = 0;
            $workers['add_own'] = 0;
            $offices['add_new'] = 0;
            $offices['add_own'] = 0;
            $scheduler['add_new'] = 0;
            $scheduler['add_own'] = 0;
            $finance['add_new'] = 0;
            $finance['add_own'] = 0;
            //
            $clients['edit'] = 0;
            $groups['edit'] = 0;
            $workers['edit'] = 0;
            $offices['edit'] = 0;
            $scheduler['edit'] = 0;
            $finance['edit'] = 0;
            //
            $clients['close'] = 0;
            $groups['close'] = 0;
            $workers['close'] = 0;
            $offices['close'] = 0;
            $scheduler['close'] = 0;
            $finance['close'] = 0;
            //
            $clients['reopen'] = 0;
            $groups['reopen'] = 0;
            $workers['reopen'] = 0;
            $offices['reopen'] = 0;
            $scheduler['reopen'] = 0;
            $finance['reopen'] = 0;
            //
            $clients['add_worker'] = 0;
            $groups['add_worker'] = 0;
            $workers['add_worker'] = 0;
            $offices['add_worker'] = 0;
            $scheduler['add_worker'] = 0;
            $finance['add_worker'] = 0;
            //
        }
    }

?>