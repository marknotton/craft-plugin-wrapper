<?php
namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;

class wrapper extends \Twig_Extension {

  public function getName() {
    return Craft::t('Wrapper');
  }

  public function getFilters() {
    return array(
      'wrap'   => new Twig_Filter_Method( $this, 'wrapFilter',   array('is_safe' => array('html')) ),
      'unwrap' => new Twig_Filter_Method( $this, 'unwrapFilter', array('is_safe' => array('html')) ),
      'h1'     => new Twig_Filter_Method( $this, 'h1Filter',     array('is_safe' => array('html')) ),
      'h2'     => new Twig_Filter_Method( $this, 'h2Filter',     array('is_safe' => array('html')) ),
      'h3'     => new Twig_Filter_Method( $this, 'h3Filter',     array('is_safe' => array('html')) ),
      'h4'     => new Twig_Filter_Method( $this, 'h4Filter',     array('is_safe' => array('html')) ),
      'h5'     => new Twig_Filter_Method( $this, 'h5Filter',     array('is_safe' => array('html')) ),
      'h6'     => new Twig_Filter_Method( $this, 'h6Filter',     array('is_safe' => array('html')) ),
      'p'      => new Twig_Filter_Method( $this, 'pFilter',      array('is_safe' => array('html')) ),
      'span'   => new Twig_Filter_Method( $this, 'spanFilter',   array('is_safe' => array('html')) ),
      'ol'     => new Twig_Filter_Method( $this, 'olFilter',     array('is_safe' => array('html')) ),
      'ul'     => new Twig_Filter_Method( $this, 'ulFilter',     array('is_safe' => array('html')) ),
      'li'     => new Twig_Filter_Method( $this, 'liFilter',     array('is_safe' => array('html')) ),
      'div'    => new Twig_Filter_Method( $this, 'divFilter',    array('is_safe' => array('html')) ),
      'section'=> new Twig_Filter_Method( $this, 'sectionFilter',array('is_safe' => array('html')) )
    );
  }

  private $singletons = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'link', 'meta','param', 'source');

  // Usage:  {{ "foobar"|wrap('h1') }}
  // Output: <h1>foobar</h1>

  // Usage:  {{ "foobar"|wrap('h1', 'className', ['foo', 'bar']) }}
  // Output: <h1 class="className" data-foo="bar">foobar</h1>

  // Usage:  {{ 'http://lorempixel.com/g/400/200'|wrap('ul li img cite', 'test', ['foo', 'bar']) }}
  // Output:
  // <ul class="test" data-foo="bar">
  //   <li>
  //     <img src="http://lorempixel.com/g/400/200" alt="http://lorempixel.com/g/400/200">
  //     <cite>http://lorempixel.com/g/400/200</cite>
  //   </li>
  // </ul>

  public function wrapFilter() {

    // Atleast one symbol sting arugment should be passed
    if ( func_num_args() < 1 ){
      return false;
    }

    // The first argument is the entry that is automatically passed.
    $html = func_get_arg(0);

    // Return false is no MTML is passed
    if ( empty($html) ){
      return false;
    }

    // Remove the first argument and set the argumetns array
    $arguments = array_slice(func_get_args(), 1);

    // Blanks settings
    $elements = null;
    $class    = null;
    $data     = null;
    $count    = null;
    $first    = false;
    $openers  = array();
    $closers  = array();

    if ( !empty($arguments) ) {

      // Loop through to see if there are any numeric values. If so, assume this is a count
      foreach ($arguments as &$setting) {
        if (is_numeric($setting) && is_null($count) ) {
          $count = $setting;
        }
      }
      // Check if any strings in the arguments contain the %i query and replace with the count
      if (!is_null($count)) {
        // print_r($arguments);

        array_walk_recursive($arguments, function(&$value, &$key) use($count) {
          if ( is_string($value) ) {
            $value = str_replace('%i', $count, $value);
          }
        });
      }

      // Loop through arguments and define settings
      foreach ($arguments as &$setting) {

        // Element and class
        if (is_string($setting)) {
          if ( is_null($elements) ) {
            $elements = $setting;
          } else if ( is_null($class) ) {
            $class = $setting;
          }
        }

        // Data
        if (is_array($setting)) {
          if ( is_null($data) && count($setting) == 2 ) {
            $data = $setting;
          }
        }

      }
    }

    if (isset($elements)) {

      $elementsArray = explode(' ', $elements);

      foreach ($elementsArray as &$element ) {
        $singleton = false;

        if (in_array($element, $this->singletons)) {
          $singleton = true;
          switch ($element) {
            case "base":
              $output = "<base href='".$html."'>";
            break;
            case "img":
              $output = "<img src='".$html."' alt='".$html."'>";
            break;
            case "a":
              $output = "<a href='".$html."''>";
            break;
            case "embed":
              $output = "<embed src='".$html."'>";
            break;
            case "link":
              $output = "<link href='".$html."'>";
            break;
            case "source":
              $output = "<source src='".$html."'>";
            break;
          }

          array_push($openers, $output);

        } else {

          if (!$first) {
            $first = true;

            $firstElement = '<'.$element;

            // If selector starts with a hash, define an ID instead of a class
            if ( isset($class) ) {
              $firstElement .= ($class[0] == '#') ? ' id="'.str_replace("#", "", $class).'"' : ' class="'.$class.'"';
            }

            // If array is passed with two elements, assume the first is a data-attribute name, and the second is the data attribute value
            if ( isset($data) ) {
              $firstElement .= ' data-'.$data[0].'="'.$data[1].'"';
            }

            array_push($openers, $firstElement.'>');

          } else {

            array_push($openers, '<'.$element.'>');

          }

          if (!$singleton) {
            array_push($closers, '</'.$element.'>');
          }
        }
      }

      return implode("",$openers).(!$singleton ? rtrim($html) : null).implode("", array_reverse($closers));

    } else {
      return $html;
    }
  }

  // Shorthand filters
  // {{ entry.title|h1 }}
  public function h1Filter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'h1', $selector, $data);
  }

  // {{ entry.title|h2 }}
  public function h2Filter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'h2', $selector, $data);
  }

  // {{ entry.title|h3 }}
  public function h3Filter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'h3', $selector, $data);
  }

  // {{ entry.title|h4 }}
  public function h4Filter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'h4', $selector, $data);
  }

  // {{ entry.title|h5 }}
  public function h5Filter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'h5', $selector, $data);
  }

  // {{ entry.title|h6 }}
  public function h6Filter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'h6', $selector, $data);
  }

  // {{ entry.title|p }}
  public function pFilter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'p', $selector, $data);
  }

  // {{ entry.title|span }}
  public function spanFilter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'span', $selector, $data);
  }

  // {{ entry.title|ol }}
  public function olFilter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'ol', $selector, $data);
  }

  // {{ entry.title|ul }}
  public function ulFilter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'ul', $selector, $data);
  }

  // {{ entry.title|li }}
  public function liFilter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'li', $selector, $data);
  }

  // {{ entry.title|div }}
  public function divFilter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'div', $selector, $data);
  }

  // {{ entry.title|section }}
  public function sectionFilter($html, $selector=null, $data=null) {
    return $this->wrapFilter($html, 'section', $selector, $data);
  }

}
