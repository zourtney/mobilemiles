# Makefile for MobileMiles.
# Copyright 2011 zourtney@randomland.net
# 
# System commands
WGET ?= $(shell which wget)
ifeq ($(strip $(WGET)),)
	WGET = curl -O
endif

MINIFY = java -jar minify/yuicompressor-2.4.6.jar --charset utf-8 -o

# Source directories
SRC = .
IMAGES_SRC = ${SRC}/images
CSS_SRC = ${SRC}/css
JS_SRC = ${SRC}/js
PAGES_SRC = ${SRC}/pages
SCRIPTS_SRC = ${SRC}/scripts
TMPL_SRC = ${SRC}/templates

# Output directories
DEST = release
CSS_DEST = ${DEST}/css
IMAGES_DEST = ${DEST}/images
JS_DEST = ${DEST}/js
JS_TMP = ${DEST}/js/tmp
PAGES_DEST = ${DEST}/pages
SCRIPTS_DEST = ${DEST}/scripts
TMPL_DEST = ${DEST}/templates

# Output files to copy straight over
STATIC_FILES = cache.manifest favicon.ico index.php license.txt
JS_FILES = ${JS_SRC}/jquery.livequery/jquery.livequery.js \
           ${JS_SRC}/jquery.timeago/jquery.timeago.js \
           ${JS_SRC}/jquery.store/json.js \
           ${JS_SRC}/jquery.store/jquery.store.js \
           ${JS_TMP}/jquery.tmpl.min.js \
           ${JS_SRC}/utils.js \
           ${JS_SRC}/app.js
JS_FILES_MONOLITH = ${JS_DEST}/mobilemiles.js
JS_FILES_MONOLITH_MIN = ${JS_DEST}/mobilemiles.min.js

# 
# Build all: compiles external sources, minifies, and outputs everything to
# a `releases` directory.
# 
all: clean structure dependencies minifyjs static
	@@echo MobileMiles, Copyright 2011 zourtney@randomland.net

# -----------------------------------------------------------------------------

# 
# Removes the entire output directory
# 
clean:
	rm -rf ${DEST}

# 
# Creates the output directory structure
# 
structure:
	mkdir ${DEST} ${CSS_DEST} ${IMAGES_DEST} ${JS_DEST} ${PAGES_DEST} ${SCRIPTS_DEST} ${TMPL_DEST} ${JS_TMP}

# 
# Dependencies, pulls latest production copies from CDNs.
# 
dependencies: jquery jquerymobile jquerymobilecss jquerytmpl

jquery:
	cd ${JS_DEST};${WGET} http://code.jquery.com/jquery-latest.min.js

jquerymobile:
	cd ${JS_DEST};${WGET} http://code.jquery.com/mobile/latest/jquery.mobile.min.js

jquerymobilecss:
	cd ${CSS_DEST};${WGET} http://code.jquery.com/mobile/latest/jquery.mobile.min.css

jquerytmpl:
	cd ${JS_TMP};${WGET} http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js

# 
# Minify JavaScript and CSS
# 
minifyjs:
	cat ${JS_FILES} > ${JS_FILES_MONOLITH} ;
	${MINIFY} ${JS_FILES_MONOLITH_MIN} ${JS_FILES_MONOLITH} ;
	rm -rf ${JS_TMP} ${JS_FILES_MONOLITH}
# cd ${JS_DEST};find * -maxdepth 0 -name '${JS_FILES_MONOLITH_MIN}' -prune -o -exec rm -rf '{}' ';'

# 
# Copy static resources over
# 
static:
	cp -r ${CSS_SRC}/ ${CSS_DEST}/ ;
	cp -r ${IMAGES_SRC}/ ${IMAGES_DEST}/ ;
	cp -r ${PAGES_SRC}/ ${PAGES_DEST}/ ;
	cp -r ${SCRIPTS_SRC}/ ${SCRIPTS_DEST}/ ;
	cp -r ${TMPL_SRC}/ ${TMPL_DEST}/ ;
	cp -r ${STATIC_FILES} ${DEST}/