SHELL := /bin/bash

test:
	./vendor/bin/phpunit

test-coverage:
	./vendor/bin/phpunit --coverage-html coverage

test-preprocessing:
	./vendor/bin/phpunit tests/IDCF/PreprocessingTest.php

test-weighting:
	./vendor/bin/phpunit tests/IDCF/WeightingTest.php