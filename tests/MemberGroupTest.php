<?php

namespace Zirak\MemberUserManagement\Test;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Group;
use SilverStripe\Security\Member;

class MemberGroupTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'fixtures.yml';

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    /*public function testUpdateCMSFields()
    {
        //$fields = Injector::inst()->get(Group::class)->getCMSFields();
        //$groupList = $fields->dataFieldByName('ParentID');
        //$administratorGroup = Group::get()->filter('Code', 'administrators')->column('ID');

        //$this->assertEquals($administratorGroup, $groupList->getDisabledItems());
    }*/

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testCanCreate()
    {
        $noPermission = $this->objFromFixture(Member::class, 'nopermission');
        $permission = $this->objFromFixture(Member::class, 'permission');
        $noPermission2 = $this->objFromFixture(Member::class, 'nopermission2');

        $this->assertFalse(Injector::inst()->get(Group::class)->canCreate($noPermission));
        $this->assertTrue(Injector::inst()->get(Group::class)->canCreate($permission));
        $this->assertFalse(Injector::inst()->get(Group::class)->canCreate($noPermission2));
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testCanEdit()
    {
        $noPermission = $this->objFromFixture(Member::class, 'nopermission');
        $noPermission2 = $this->objFromFixture(Member::class, 'nopermission2');
        $permission = $this->objFromFixture(Member::class, 'permission');

        $group = $this->objFromFixture(Group::class, 'samplegroup');

        $this->assertFalse($group->canEdit($noPermission));
        $this->assertFalse($group->canEdit($noPermission2));
        $this->assertTrue($group->canEdit($permission));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        $noPermission = $this->objFromFixture(Member::class, 'nopermission');
        $noPermission2 = $this->objFromFixture(Member::class, 'nopermission2');
        $permission = $this->objFromFixture(Member::class, 'permission');

        $group = Injector::inst()->create(Group::class);

        $this->assertFalse($group->canDelete($noPermission));
        $this->assertTrue($group->canDelete($permission));
        $this->assertFalse($group->canDelete($noPermission2));
    }

    /**
     *
     */
    /*public function testCanView()
    {
        $noPermission = $this->objFromFixture(Member::class, 'nopermission');
        $noPermission2 = $this->objFromFixture(Member::class, 'nopermission2');
        $permission = $this->objFromFixture(Member::class, 'permission');

        $toTest = Member::get()->byID($noPermission->ID);

        $this->assertTrue($toTest->canView($noPermission));
        $this->assertTrue($toTest->canView($permission));
        $this->assertFalse($toTest->canView($noPermission2));
    }*/
}
