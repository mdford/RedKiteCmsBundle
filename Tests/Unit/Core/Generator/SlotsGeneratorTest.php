<?php
/**
 * This file is part of the RedKiteLabsRedKiteCmsBundle and it is distributed
 * under the MIT License. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) RedKite Labs <webmaster@redkite-labs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    MIT License
 *
 */

namespace RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Tests\Unit\Core\Generator;

use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Generator\SlotsGenerator;
use org\bovigo\vfs\vfsStream;

/**
 * TemplateParserTest
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
class SlotsGeneratorTest extends Base\GeneratorBase
{
    private $slotsGenerator;

    protected function setUp()
    {
        parent::setUp();

        $this->root = vfsStream::setup('root', null, array(
            'slots',
            'Slots' => array(),
            'app' => array(
                'Resources' => array(
                    'views' => array(
                        'MyThemeBundle' => array(
                        ),
                    ),
                ),
            ),
        ));
        
        $skeletonDir = __DIR__ . '/../../../../Resources/skeleton';
        if ( ! is_dir($skeletonDir)) {
            $skeletonDir = __DIR__ . '/Resources/skeleton';
            if ( ! is_dir($skeletonDir)) {
                $this->markTestSkipped(
                    'skeleton dir is not available.'
                );
            }
        }
        vfsStream::copyFromFileSystem($skeletonDir, $this->root);

        $this->slotsGenerator = new SlotsGenerator(vfsStream::url('root/app-theme'));
    }

    public function testSlotsConfigurationFileHasBeenGenerated()
    {
        $contents = '<div id="logo">' . PHP_EOL;
        $contents .= '{% block logo %}' . PHP_EOL;
        $contents .= '{# BEGIN-SLOT' . PHP_EOL;
        $contents .= '   name: logo' . PHP_EOL;
        $contents .= '   repeated: site' . PHP_EOL;
        $contents .= '   fake: site' . PHP_EOL;
        $contents .= '   blockType: script' . PHP_EOL;
        $contents .= '   htmlContent: |' . PHP_EOL;
        $contents .= '       <img src="/uploads/assets/media/business-website-original-logo.png" title="Progress website logo" alt="Progress website logo" />' . PHP_EOL;
        $contents .= 'END-SLOT #}' . PHP_EOL;
        $contents .= '{{ renderSlot(\'logo\') }}' . PHP_EOL;
        $contents .= '{% endblock %}' . PHP_EOL;
        $contents .= '</div>';
        file_put_contents(vfsStream::url('root/Slots/slots.html.twig'), $contents);

        $information = $this->parser->parse(vfsStream::url('root/Slots'), vfsStream::url('root/app'), 'MyThemeBundle');
        $message = $this->slotsGenerator->generateSlots(vfsStream::url('root/slots'), 'FakeThemeBundle', $information['slots']);

        $expected = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;
        $expected .= '<container xmlns="http://symfony.com/schema/dic/services"' . PHP_EOL;
        $expected .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL;
        $expected .= '        xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">' . PHP_EOL;
        $expected .= PHP_EOL;
        $expected .= '    <services>' . PHP_EOL;
        $expected .= '        <service id="fake_theme.theme.template.slots.logo" class="%red_kite_labs_theme_engine.slot.class%" public="false">' . PHP_EOL;
        $expected .= '            <argument type="string">logo</argument>' . PHP_EOL;
        $expected .= '            <argument type="collection" >' . PHP_EOL;
        $expected .= '                <argument key="repeated">site</argument>' . PHP_EOL;
        $expected .= '                <argument key="blockType">script</argument>' . PHP_EOL;
        $expected .= '                <argument key="htmlContent">' . PHP_EOL;
        $expected .= '                    <![CDATA[<img src="/uploads/assets/media/business-website-original-logo.png" title="Progress website logo" alt="Progress website logo" />]]>' . PHP_EOL;
        $expected .= '                </argument>' . PHP_EOL;
        $expected .= '            </argument>' . PHP_EOL;
        $expected .= '            <tag name="fake_theme.theme.template.slot" />' . PHP_EOL;
        $expected .= '        </service>' . PHP_EOL;
        $expected .= '    </services>' . PHP_EOL;
        $expected .= '</container>';

        $this->assertFileExists(vfsStream::url('root/slots/slots.xml'));
        $this->assertEquals($expected, file_get_contents(vfsStream::url('root/slots/slots.xml')));

        $expected = '<error>The argument site assigned to the logo slot is not recognized</error>The template\'s slots <info>slots.xml</info> has been generated into <info>vfs://root/slots</info>';
        $this->assertEquals($expected, $message);
    }

    public function testSlotsConfigurationFileHasBeenGeneratedFromTheRealTheme()
    {
        $this->importDefaultTheme();
        $information = $this->parser->parse(vfsStream::url('root/Slots'), vfsStream::url('root/app'), 'MyThemeBundle');
        $message = $this->slotsGenerator->generateSlots(vfsStream::url('root/slots'), 'FakeThemeBundle', $information['slots']);

        $this->assertFileExists(vfsStream::url('root/slots/slots.xml'));
        $expected = 'The template\'s slots <info>slots.xml</info> has been generated into <info>vfs://root/slots</info>';
        $this->assertEquals($expected, $message);
    }

    private function initEmptyServicesFile()
    {
        $expected = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;
        $expected .= '<container xmlns="http://symfony.com/schema/dic/services"' . PHP_EOL;
        $expected .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL;
        $expected .= '        xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">' . PHP_EOL;
        $expected .= PHP_EOL;
        $expected .= '    <services>' . PHP_EOL;
        $expected .= '    </services>' . PHP_EOL;
        $expected .= '</container>';

        return $expected;
    }

    private function initOneSlotServicesFile()
    {
        $expected = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;
        $expected .= '<container xmlns="http://symfony.com/schema/dic/services"' . PHP_EOL;
        $expected .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL;
        $expected .= '        xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">' . PHP_EOL;
        $expected .= PHP_EOL;
        $expected .= '    <services>' . PHP_EOL;
        $expected .= '        <service id="fake_theme.theme.template.slots.logo" class="%red_kite_labs_theme_engine.slot.class%" public="false">' . PHP_EOL;
        $expected .= '            <argument type="string">logo</argument>' . PHP_EOL;
        $expected .= '            <tag name="fake_theme.theme.template.slot" />' . PHP_EOL;
        $expected .= '        </service>' . PHP_EOL;
        $expected .= '    </services>' . PHP_EOL;
        $expected .= '</container>';

        return $expected;
    }
}