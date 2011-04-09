# TMPL Doc (BETA) #

A template documentor for ExpressionEngine 2

## Installation

* Copy the /system/expressionengine/third_party/tmpldoc/ folder to your /system/expressionengine/third_party/ folder
* Copy the /themes/third_party/tmpldoc/ folder to your /themes/third_party/ folder
* Login to the control panel and go to Add-Ons > Modules.
* Install TMPL Doc

## Usage

* Comment associated with a tag (needs the # signs):  

		{!#--
			Displays the currently viewed news article.
			@tag {exp:channel:entries} 
			@param channel channel that holds the news articles
		--#}
		{exp:channel:entries channel="news" limit="1"} 
			{title}
		{/exp:channel:entries}

* Comment not associated with a tag:  

		{!--
			This template displays the currently viewed news article.
			@todo Debug routing conditionals
		--}
	
* User Defined Globals (prefix global name with gbl):  

		{gbl-name-of-global}

* Snippets (prefix snippet name with snip):  

		{snip-name-of-snippet}


## Issues

Please log any issues or problems here: <https://github.com/themusicman/TMPL-Doc/issues>

