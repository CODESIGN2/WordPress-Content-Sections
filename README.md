# WordPress-Content-Sections
WordPress Plugin to create reusable content section areas

This is a simple plugin designed to fulfil rapid prototyping, and consistent central updates of your website content. It's beauty is in it's simplicity and versatility.

## Usage

### Shortcode

**CODE**
```
[content_section name="title/slug"]
```

**Output**
```html
<p>Lorem Ipsum Dolur (I have a featured image)</p>\n
```

Like I said, it's simple

### Ajax

#### Single Content Section

**CODE**
```JavaScript
var url = "/wp-admin/admin-ajax.php?action=get_content_section&name=test";
// retrieve via your chosen library
```

**Output**  
```JSON
{
  "content":"<p>Lorem Ipsum Dolur (I have a featured image)<\/p>\n"
}
```

#### All Content Sections  

Gets a JSON formatted response of all content sections

**CODE**  
```JavaScript
var url = "/wp-admin/admin-ajax.php?action=get_content_sections";
// retrieve via your chosen library
```

**Output**
```JSON
{
  "wpurl":"http:\/\/www.somesite.co.uk",
  "results":[
    {
      "ID":51,
      "title":"test",
      "author":"cd2_admin",
      "content":"<p>Lorem Ipsum Dolur<\/p>\n",
      "thumbnail":false
    }
    {
      "ID":97,
      "title":"test2",
      "author":"cd2_admin",
      "content":"<p>Lorem Ipsum Dolur (I have a featured image)<\/p>\n",
      "thumbnail":"http:\/\/www.somesite.co.uk\/wp-content\/uploads\/content_section_image.jpg"
    }
  ]
}
```

## Notes
uses the filter `the_content` so oembed etc should work as well as other plugins affecting `the_content`. We do not recommend embedding any CSS in the content (this should be the task of CSS)
