# enterdraw

a moodle filter that decides which text to show depending on the state of a button.

the button state is stored in the user preference (not editable, but potentially queryable if you needed to).

This means the user can only submit the form once on that page. Like you might need were they voting on something, or /enter/ing a /draw/...

## How to use

Probably best used inside a html block or a page activity. Enable the filter on the page or context as appropriate, then in the content put in some markers to tell the filter where to draw the form.

```
html can go here before

[[ENTERDRAW:START]]

Put in the stuff you want to appear when the button hasn't been pushed

[[ENTERDRAW:BUTTON The button text goes here]]

[[ENTERDRAW:ELSE]]

Your vote counts! Get your socks on, it's going to be a wild ride.

[[ENTERDRAW:END]]

html can go here too

```

## stuff

The form is just a regular `single_button` which is used allthroughout Moodle. The form is wrapped in a div with the classname `enterdraw` in case you want to style things differently.

The submission causes a `\filter_enterdraw\event\event_submitted` event to be triggered. So consume that however you wish.

## Licence 

GPL3