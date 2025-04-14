# Quelques infos complémentaires

## ~/.bash_aliases
```shell
alias sf-reset="composer install && yarn install && yarn dev"
alias sf-start="docker compose up -d && symfony serve"
git-prune() {
    git fetch --prune;
    git branch -vv | awk '/(gone|disparue)]/ {print $1}' | xargs git branch -D;
}
```
## ~/.profile
```shell
# set PATH for composer
PATH="./bin:./vendor/bin:$HOME/.composer/vendor/bin:$PATH"
```
