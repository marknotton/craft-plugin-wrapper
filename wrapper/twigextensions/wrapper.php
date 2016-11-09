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
      'section'=> new Twig_Filter_Method( $this, 'sectionFilter',array('is_safe' => array('html')) ),
      'img'    => new Twig_Filter_Method( $this, 'imgFilter',    array('is_safe' => array('html')) )
    );
  }

  private $singletons = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'link', 'meta','param', 'source');

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
    $title    = null;
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

          if ( $elements === 'img' ) {
            if ( is_null($elements) ) {
              $elements = $setting;
            } else if ( is_null($title) ) {
              $title = $setting;
            } else if ( is_null($title) ) {
              $class = $setting;
            }
          } else {
            if ( is_null($elements) ) {
              $elements = $setting;
            } else if ( is_null($class) ) {
              $class = $setting;
            }
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
              $title = is_null($title) ? $html : $title;
              $output = "<img src='".$html."' alt='".$title."'>";
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
  public function h1Filter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'h1', $class, $data);
  }

  // {{ entry.title|h2 }}
  public function h2Filter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'h2', $class, $data);
  }

  // {{ entry.title|h3 }}
  public function h3Filter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'h3', $class, $data);
  }

  // {{ entry.title|h4 }}
  public function h4Filter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'h4', $class, $data);
  }

  // {{ entry.title|h5 }}
  public function h5Filter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'h5', $class, $data);
  }

  // {{ entry.title|h6 }}
  public function h6Filter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'h6', $class, $data);
  }

  // {{ entry.title|p }}
  public function pFilter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'p', $class, $data);
  }

  // {{ entry.title|span }}
  public function spanFilter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'span', $class, $data);
  }

  // {{ entry.title|ol }}
  public function olFilter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'ol', $class, $data);
  }

  // {{ entry.title|ul }}
  public function ulFilter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'ul', $class, $data);
  }

  // {{ entry.title|li }}
  public function liFilter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'li', $class, $data);
  }

  // {{ entry.title|div }}
  public function divFilter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'div', $class, $data);
  }

  // {{ entry.title|section }}
  public function sectionFilter($html, $class=null, $data=null) {
    return $this->wrapFilter($html, 'section', $class, $data);
  }

  // {{ entry.title|section }}
  public function imgFilter($html, $title=null, $class=null, $data=null) {
    return $this->wrapFilter($html, 'img', $class, $data, $title);
  }

}
