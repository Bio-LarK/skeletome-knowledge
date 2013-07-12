<?php
class NotificationController extends EntityAPIController {
    public function create(array $values = array()) {
        global $user;
        $values += array(
            'created' => REQUEST_TIME,
            'changed' => REQUEST_TIME,
            'uid' => $user->uid,
        );
        return parent::create($values);
    }
}
