# enterdraw

a moodle filter that decides which text to show depending on the state of a button. Entries are calculated based on the current page context.

the button state is stored in the user preference (not editable, but potentially queryable if you needed to).

This means the user can only submit the form once on that page. Like you might need were they voting on something, or /enter/ing a /draw/...

Youc an also set a total so that the draw closes after N entries.

## How to use

You have to first define the content to choose

Probably best used inside a html block or a page activity. Enable the filter on the page or context as appropriate, then in the content put in some markers to tell the filter where to draw the form.

The appropriate response will be shown at the location of the `[[ENTERDRAW:OUTPUT]]` marker, all other blocks are removed from the input text.

```
html can go here before if you like

`[[ENTERDRAW:OPEN]]`Put in the html you want to appear when the button hasn't been pushed

`[[ENTERDRAW:BUTTON ` The button text `]]`
`[[/ENTERDRAW:OPEN]]`

`[[ENTERDRAW:CLOSED]]`The html to show when the draw is closed`[[/ENTERDRAW:CLOSED]]`

`[[ENTERDRAW:ENTERED]]`The html to show when the user has submitted an entry and the draw is still open.`[[/ENTERDRAW:ENTERED]]`

`[[ENTERDRAW:TOTAL]]`99`[[/ENTERDRAW:TOTAL]]`

Your vote counts! Get your socks on, it's going to be a wild ride.

`[[ENTERDRAW:OUTPUT]]`

html can go here too

```

## stuff

The form is just a regular `single_button` which is used all throughout Moodle. The form is wrapped in a div with the classname `enterdraw` in case you want to style things differently.

The submission causes a `\filter_enterdraw\event\event_submitted` event to be triggered. So consume that however you wish.

## Licence 

GPL3