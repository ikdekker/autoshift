# autoshift
Creates rosters / shifts / schemes or whatever you want to call it for employes automatically based on some given info.

### Preview

![](https://i.imgur.com/J9RcNJo.gif)


### Calendar generation

Needed input:

**Calendar layout**
- timerange, or month (carbon should do well for direct input parsing)
- config, if not fixed
- employee of the month
- title

**Metadata**
- generation type, sequential, or fair/equal distribution (sequential, distribute, weighted)
- user availability, yes/no on sequential, other on distribute / weighted
- week-fill / week layout -> crontablike config, 1-5 for mo-vr
- display-config, name, double-name, colored names (optionally output legend)
