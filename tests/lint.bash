#!/bin/bash

shopt -s globstar

set -e

for x in **/*php; do
	php -l "$x";
done
