# data-syntax-release-status

Data syntax for release status — how far the testing of a Valotalive (or Valota
Wall) release has got.

Produced by [source-releases](https://github.com/Valota/source-releases), which
gets its numbers from the `releases` / `wall-releases` tooling. Carries **counts
only**: the release in progress (`current`), the latest shipped release
(`previous`) and the progress of the current release over time (`history`).
Commit hashes, commit messages, Jira keys and task titles are deliberately not
part of it, since the data is shown on screens; the details are behind the
source's Use button, for channel-server administrators only.
