<?php


defined('MOODLE_INTERNAL') || die();


/**
 * PHPUnit data generator testcase
 *
 * @package    mod_league
 * @group      mod_league
 * @category   phpunit
 * @copyright  2018 Miguel Romeral
 */
class mod_league_generator_testcase extends advanced_testcase {
    public function test_generator() {
        global $DB, $SITE;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('league'));

        /** @var mod_league_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_league');
        $this->assertInstanceOf('mod_league_generator', $generator);
        $this->assertEquals('league', $generator->get_modulename());

        $generator->create_instance(array('course'=>$SITE->id));
        $generator->create_instance(array('course'=>$SITE->id));
        $league = $generator->create_instance(array('course'=>$SITE->id));
        $this->assertEquals(3, $DB->count_records('league'));

        $cm = get_coursemodule_from_instance('league', $league->id);
        $this->assertEquals($league->id, $cm->instance);
        $this->assertEquals('league', $cm->modname);
        $this->assertEquals($SITE->id, $cm->course);

        $context = context_module::instance($cm->id);
        $this->assertEquals($league->cmid, $context->instanceid);
    }
}
