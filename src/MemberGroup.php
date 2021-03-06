<?php

namespace Zirak\MemberUserManagement\Extension;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Group;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

class MemberGroup extends DataExtension
{
    /**
     * Remove the administrators group from the possible parent group
     *
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->canEdit() && !Permission::checkMember(Security::getCurrentUser(), 'ADMIN')) {
            $fields->dataFieldByName('ParentID')->setDisabledItems([
                Group::get()->filter('Code', 'administrators')->first()->ID,
            ]);
        };
    }

    /**
     * Check if the current user can modify the group
     *
     * @param Member $member
     *
     * @return bool
     */
    private function isAdmin(Member $member)
    {
        if ($member == null) {
            return false;
        }

        if ($member->inGroup('users-manager')) {
            return true;
        }

        if (Permission::checkMember($member, 'ADMIN')) {
            return true;
        }

        return false;
    }

    /**
     * @param null|Member $member
     *
     * @return bool
     */
    public function canCreate($member = null)
    {
        if (!$member instanceof Member) {
            $member = Security::getCurrentUser();
        }

        return $this->isAdmin($member);
    }

    /**
     * @param null|Member $member
     *
     * @return bool
     */
    public function canEdit($member = null)
    {
        if (!$member instanceof Member) {
            $member = Security::getCurrentUser();
        }

        return $this->isAdmin($member) && $this->owner->Code !== 'administrators';
    }

    /**
     * @param $member
     *
     * @return bool
     */
    public function canView($member = null)
    {
        if (!$member instanceof Member) {
            $member = Security::getCurrentUser();
        }

        if ($member === null) {
            return false;
        }

        return $this->isAdmin($member);
    }

    /**
     * @param null|Member $member
     *
     * @return bool
     */
    public function canDelete($member = null)
    {
        if (!$member instanceof Member) {
            $member = Security::getCurrentUser();
        }

        return $this->isAdmin($member) && $this->owner->Code !== 'administrators';
    }

    /**
     * Add a specific group in order to enable users/groups management
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        $group = Group::get()->filter('Code', 'users-manager')->first();
        if (!$group) {
            $usersManagerGroup = Group::create();
            $usersManagerGroup->Code = 'users-manager';
            $usersManagerGroup->Title = _t('Group.DefaultGroupTitleUsersManager', 'Users Manager');
            $usersManagerGroup->write();
            Permission::grant($usersManagerGroup->ID, 'CMS_ACCESS_SecurityAdmin');
        }
    }
}
