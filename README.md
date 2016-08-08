# Wrapper *for Craft CMS*
Wrap or unwrap data around an array of HTML markup tags.

##Wrapper

####Settings:
| Option | Type | Description |
-- | -- | --
| Tags | String | A single string where multiple tags are comma-delimited |
| Class | String | Class name/s that get applied to the first tag |
| Data | Array | A single array of two strings will make up a data-attribute on the first tag |

####Basic Usage:
```
{{ entry.title|wrap('h1') }}
```

You can also use the following common HTML tag shortcuts :
| h1 | h2 | h3 | h4 | h5 | h6 | p | span | ol | ul | li | div | section |
-- | -- | -- | -- | -- | -- | - | ---- | -- | -- | -- | --- | -------

Which means you could also do this:
```
{{ entry.title|h1 }}
```

####Basic Output:
Both methods output the same thing:

```
<h1>Entry Title</h1>
```
---
####Advance Usage:
```
{{ '/assets/images/logo.png'|wrap('ul li img cite', 'test', ['foo', 'bar']) }}
```
####Advance Output:
```
<ul class="test" data-foo="bar">
  <li>
    <img src="/assets/images/logo.png" alt="/assets/images/logo.png">
    <cite>/assets/images/logo.png</cite>
  </li>
</ul>
```
---

####Singletons:
Some singletons will fallback and use the content as part of it's formatting.

```
 {{ entry.someUrl|wrap('base') }}
 <base href='http://www.someurl.uk'>
```
```
 {{ entry.someUrl|wrap('img') }}
 <img src='http://www.someurl.uk' alt='http://www.someurl.uk'>
```
```
 {{ entry.someUrl|wrap('embed') }}
 <embed src='http://www.someurl.uk'>
```
```
 {{ entry.someUrl|wrap('link') }}
 <link href='http://www.someurl.uk'>
```
```
 {{ entry.someUrl|wrap('source') }}
 <source src='http://www.someurl.uk'>
```
All other singletons will simply be ignored:
| area | br | col | command | hr | input | meta | param |
-- | -- | -- | -- | -- | -- | -- | -- | -- | --


----

##Unwrapper
This filter removes **all** tags, except for tags passed into the filter.

####Basic Usage:
```
{{ "<h1><span><cite> Page Title </cite></span></h1>"|unwrap('h1') }}
```
####Basic Output:
**Before:**
```
<h1><span><cite> Page Title </cite></span></h1>
```
**After:**
```
<h1>Page Title</h1>
```
