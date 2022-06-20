<?php
require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class SeleniumTests extends TestCase
{
    protected $webDriver;

    public function setUp(): void
    {
        $SERVER_URL = 'http://localhost:4444';
        $this->webDriver = RemoteWebDriver::create($SERVER_URL, DesiredCapabilities::chrome());
        $this->webDriver->manage()->window()->maximize();
    }

    function testLogin()
    {
        try {
            $this->webDriver->get('https://letteryourself.herokuapp.com');

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[1]/input'))->sendKeys('suleymanoner1999@gmail.com');
            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[2]/input'))->sendKeys('');
            $this->webDriver->findElement(WebDriverBy::id('login-link'))->click();
            sleep(3);

            $this->assertEquals("Letter Yourself", $this->webDriver->getTitle());
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function testAddLetter()
    {
        try {
            $this->webDriver->get('https://letteryourself.herokuapp.com');

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[1]/input'))->sendKeys('suleymanoner1999@gmail.com');
            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[2]/input'))->sendKeys('');
            $this->webDriver->findElement(WebDriverBy::id('login-link'))->click();
            sleep(3);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/main/section[1]/div[1]/div/button'))->click();
            sleep(2);

            $this->webDriver->findElement(WebDriverBy::name('title'))->sendKeys('Test Letter');
            $this->webDriver->findElement(WebDriverBy::name('send_at'))->sendKeys('2022-07-15T08:30');
            $this->webDriver->findElement(WebDriverBy::name('body'))->sendKeys('Test letter body.');
            $this->webDriver->findElement(WebDriverBy::name('receiver_email'))->sendKeys('test@receiver.com');
            sleep(3);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/main/section[1]/div[2]/div[2]/div/form/div[3]/button[2]'))->click();
            sleep(3);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/main/section[1]/div[1]/div/div[2]/div/div/div[2]/div/table/thead/tr/th[1]'))->click();
            sleep(2);

            $emptyOrNot = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/main/section[1]/div[1]/div/div[2]/div/div/div[2]/div/table/tbody/tr/td'))->getText();
            $this->assertNotEquals("Nothing found", $emptyOrNot);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function testCommunicationButton()
    {
        try {
            $this->webDriver->get('https://letteryourself.herokuapp.com');

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[1]/input'))->sendKeys('suleymanoner1999@gmail.com');
            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[2]/input'))->sendKeys('');
            $this->webDriver->findElement(WebDriverBy::id('login-link'))->click();
            sleep(3);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/nav/div[2]/div/ul/li[2]/a'))->click();
            sleep(3);

            $title =  $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/nav/div[2]/div/ul/li[2]'))->getText();
            $this->assertEquals("Communication", $title);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function testAdminButton()
    {
        try {
            $this->webDriver->get('https://letteryourself.herokuapp.com');

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[1]/input'))->sendKeys('suleymanoner1999@gmail.com');
            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[2]/input'))->sendKeys('');
            $this->webDriver->findElement(WebDriverBy::id('login-link'))->click();
            sleep(3);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/nav/div[2]/div/ul/li[4]'))->click();
            sleep(3);

            $title =  $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/main/section/div/div/div[1]/div/h1'))->getText();
            $this->assertEquals("Users", $title);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function testGoUserProfile()
    {
        try {
            $this->webDriver->get('https://letteryourself.herokuapp.com');

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[1]/input'))->sendKeys('suleymanoner1999@gmail.com');
            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[2]/input'))->sendKeys('');
            $this->webDriver->findElement(WebDriverBy::id('login-link'))->click();
            sleep(3);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/nav/ul/li/a/i'))->click();
            sleep(1);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/nav/ul/li/ul/li[1]'))->click();
            sleep(3);

            $title =  $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/main/section[3]/div/div/div[1]/div/h1'))->getText();
            $this->assertEquals("User Profile", $title);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function testAdminPageSearch()
    {
        try {
            $this->webDriver->get('https://letteryourself.herokuapp.com');

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[1]/input'))->sendKeys('suleymanoner1999@gmail.com');
            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[2]/input'))->sendKeys('');
            $this->webDriver->findElement(WebDriverBy::id('login-link'))->click();
            sleep(3);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/nav/ul/li/a/i'))->click();
            sleep(1);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/nav/ul/li/ul/li[2]'))->click();
            sleep(3);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/main/section/div/div/div[2]/div/div/div[1]/div[2]/div/label/input'))->sendKeys('suleyman');
            sleep(3);

            $name = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/main/section/div/div/div[2]/div/div/div[2]/div/table/tbody/tr/td[2]'))->getText();
            $this->assertEquals("Suleyman", $name);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function testLogout()
    {
        try {
            $this->webDriver->get('https://letteryourself.herokuapp.com');

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[1]/input'))->sendKeys('suleymanoner1999@gmail.com');
            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/div[1]/div/div/div[2]/form/fieldset/div[2]/input'))->sendKeys('');
            $this->webDriver->findElement(WebDriverBy::id('login-link'))->click();
            sleep(3);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/nav/ul/li/a/i'))->click();
            sleep(1);

            $this->webDriver->findElement(WebDriverBy::xpath('/html/body/div/nav/ul/li/ul/li[4]'))->click();
            sleep(2);

            $this->assertEquals("LetterYourself Login", $this->webDriver->getTitle());
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function tearDown(): void
    {
        $this->webDriver->quit();
    }
}
