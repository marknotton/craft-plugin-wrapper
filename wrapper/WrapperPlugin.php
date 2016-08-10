<?php
namespace Craft;

class WrapperPlugin extends BasePlugin {
  public function getName() {
    return Craft::t('Wrapper');
  }

  public function getVersion() {
    return '0.1';
  }

  public function getSchemaVersion() {
    return '0.1';
  }

  public function getDescription() {
    return 'Wrap or unwrap data around an array of HTML markup tags.';
  }

  public function getDeveloper() {
    return 'Yello Studio';
  }

  public function getDeveloperUrl() {
    return 'http://yellostudio.co.uk';
  }

  public function getDocumentationUrl() {
    return 'https://github.com/marknotton/craft-plugin-wrapper';
  }

  public function getReleaseFeedUrl() {
    return 'https://raw.githubusercontent.com/marknotton/craft-plugin-wrapper/master/wrapper/releases.json';
  }

  public function addTwigExtension() {
    Craft::import('plugins.wrapper.twigextensions.wrapper');
    Craft::import('plugins.wrapper.twigextensions.unwrapper');
    return new wrapper();
    return new unwrapper();
  }
}
