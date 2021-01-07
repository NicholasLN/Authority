# Authority 3.0

**Come join the Discord! [https://discord.gg/aqjspfseDT](https://discord.gg/aqjspfseDT)**

This is the github for my game [Authority](https://www.europeanperil.com/authority)

*Authority is an indev. political game in which users can register as a politician, run for offices, run countries, play a vital part in the economic system within their countries (and others), and seize power through a variety of methods--legal, or illegal.*

---




I'm pretty new to Git/Github in general, so there may be issues with this repository. Please feel free to point those out, and feel free to fork this repo at any time to create your own testing server. Change is welcome and appreciated. We're all here together to make a good game - Phil

In order to create a test server, you will need to create a *server.json* file within the config directory, like such:

```json
{
    "user":"Enter DB Username Here",
    "password":"Enter DB Password Here",
    "host":"Enter DB Host Here",
    "database":"Enter DB Here",
    "port":"Enter DB Port Here"
}
```

This will be used to connect to a SQL DB of your choosing.

[SQL CREATE Table Queries](https://github.com/PhilScott4VT/Authority/blob/main/sql_schema.sql)
