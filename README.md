# Light persistance

For integrate to nomess:

> config/container.yaml <br>
> components : <br>
> nomess/light_persist: '%ROOT%vendor/nomess/light_persist/config/container.yaml'  

> config/cache.yaml <br>

<code> 
light_persist: <br>
&nbsp&nbsp&nbsp&nbsp        enable: true <br>
&nbsp&nbsp&nbsp&nbsp       path: light_persist/ <br>
&nbsp&nbsp&nbsp&nbsp       parameters: <br>
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp            value: [] <br>
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp            filename: [type: string, constraint: NOT_EMPTY] <br>
&nbsp&nbsp&nbsp&nbsp        revalidation_rules: [] <br>
&nbsp&nbsp&nbsp&nbsp        removed_by_cli: false <br>
&nbsp&nbsp&nbsp&nbsp        return: mixed <br>
</code>
