module TagOptionsHelper

  # http://code.google.com/p/google-code-prettify/
  def pretty_tag(html)
	if html.blank? 
	   return ""
	end
	code = stylesheet_link_tag "prettify"
	code += javascript_include_tag "prettify"
	# Note that prettyprint and lang-html are special classes for the prettifier, 'code' is ours, in liftium.css
        code += '<pre class="prettyprint lang-html code">' + "\n" + h(html) + "</pre>\n"
	code += '<script>prettyPrint()</script>'
  end
end
