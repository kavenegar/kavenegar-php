<?php

namespace Kavenegar\Tests;

use Kavenegar\KavenegarApi;

class ContactTests extends BaseTestCase
{
    private function getApi()
    {
        $api = new KavenegarApi(self::getApiKey());
        if (self::getApiKey() === "<put your apikey here>") {
            $this->markTestSkipped('Real API key not configured.');
        }
        return $api;
    }

    public function testListGroups()
    {
        $api = $this->getApi();
        $groups = $api->listGroups();
        $this->assertNotNull($groups);
    }

    public function testAddGroup()
    {
        $api = $this->getApi();
        $newGroups = $api->addGroup("AddGroupTest1", "SaaS");
        $this->assertCount(1, $newGroups);
        $group = $newGroups[0];
        $this->assertNotNull($group);

        // Delay before remove
        sleep(1);

        // Clean up
        $api->removeGroup($group->groupid);
    }

    public function testEditGroup()
    {
        $api = $this->getApi();
        $groups = $api->addGroup("EditGroupTest", "SaaS-Edit");
        $group = $groups[0];

        // Delay before edit
        sleep(1);

        $edited = $api->editGroup($group->groupid, "EditGroupTestUpdated2", null, "SaaS-Edit-Updated");
        $this->assertEquals("EditGroupTestUpdated2", $edited[0]->name);

        // Delay before remove
        sleep(1);

        // Clean up
        $api->removeGroup($edited[0]->groupid);
    }

    public function testSearchGroups()
    {
        $api = $this->getApi();
        $groups = $api->addGroup("SearchGroupTest", "SaaS-Search");
        $group = $groups[0];

        // Delay before search
        sleep(1);

        $search = $api->searchGroups("SaaS-Search");
        $this->assertNotNull($search);

        // Delay before remove
        sleep(1);

        // Clean up
        $api->removeGroup($group->groupid);
    }

    public function testRemoveGroup()
    {
        $api = $this->getApi();
        $groups = $api->addGroup("RemoveGroupTest", "SaaS-Remove");
        $group = $groups[0];

        // Delay before remove
        sleep(1);

        $remainingGroups = $api->removeGroup($group->groupid);
        $this->assertNotNull($remainingGroups);
    }

    public function testAddContact()
    {
        $api = $this->getApi();
        $groups = $api->addGroup("AddContactTest", "SaaS-Contact");
        $group = $groups[0];

        // Delay before add
        sleep(1);

        $contacts = $api->addContact($group->groupid, self::getReceptor(), "تست افزودن مخاطب");
        $this->assertNotNull($contacts);

        // Delay before remove
        sleep(1);

        // Clean up
        $api->removeGroup($group->groupid);
    }

    public function testRemoveContact()
    {
        $api = $this->getApi();
        $groups = $api->addGroup("RemoveContactTest", "SaaS-RemoveContact");
        $group = $groups[0];

        // Delay before add
        sleep(1);

        $api->addContact($group->groupid, self::getReceptor(), "تست حذف مخاطب");

        // Delay before remove contact
        sleep(1);

        $remainingContacts = $api->removeContact(null, self::getReceptor(), $group->groupid);
        $this->assertNotNull($remainingContacts);

        // Delay before remove group
        sleep(1);

        // Clean up
        $api->removeGroup($group->groupid);
    }
}
