<?php

class ZkMemberUser extends DataExtension {


    public function updateCMSFields(FieldList $fields){

        if(!Permission::checkMember(Member::currentUser(), 'ADMIN')){
            $current = Member::currentUser();
            $groupsMap = array();
            foreach($current->Groups()->exclude('Code', 'users-manager')->toArray() as $group) {
                // Listboxfield values are escaped, use ASCII char instead of &raquo;
                $groupsMap[$group->ID] = $group->getBreadcrumbs(' > ');
            }
            asort($groupsMap);

            $groupsField = ListboxField::create('DirectGroups', singleton('Group')->i18n_plural_name())
                ->setMultiple(true)
                ->setSource($groupsMap)
                ->setAttribute(
                    'data-placeholder',
                    _t('Member.ADDGROUP', 'Add group', 'Placeholder text for a dropdown'));

            $fields->insertBefore($groupsField,'DateFormat');
        }

    }

	/**
	 * Check if the current user can modify the user
	 * @param Member $member
	 * @return boolean
	 */
	private function isAdmin($member = null) {
		$retVal = false;

		$current = Member::currentUser();
		$groups = $current->Groups();

		if (Permission::checkMember($current, 'ADMIN')) {
			return true;
		}

        if(Config::inst()->get('ZkMemberUser', 'limit_users')){
            $groups = $groups->exclude('Code', 'users-manager');
            return ($this->owner->inGroups($groups));
        }else{
            foreach ($groups as $g) {
                if ($g->Code == 'users-manager') {
                    if (!Permission::checkMember($this->owner, 'ADMIN')) {
                        $retVal = true;
                        break;
                    }
                }
            }
        }
		
		return $retVal;
	}

	public function canCreate($member) {
		return $this->isAdmin($member);
	}

	public function canEdit($member) {
		if ($this->owner->ID == Member::currentUserID()) {
			return true;
		}
		return $this->isAdmin($member);
	}

	public function canView($member) {
		return $this->isAdmin($member);
	}

	public function canDelete($member) {
		return $this->isAdmin($member);
	}

}
