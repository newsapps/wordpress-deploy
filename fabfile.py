# -*- coding: utf-8 -*-
# Chicago Tribune News Applications fabfile
# No copying allowed

import os
import subprocess
import urllib

from fabric.api import *
from fabric.contrib.console import confirm
from fabric.context_managers import cd

from getpass import getpass

"""
Base configuration
"""
env.project_name = "my-wp-blog"
env.wpdomain = 'my-wp-blog.dev'
env.path = os.getcwd()

# Do you want to use git or svn for deployment?
env.strategy = 'git'

# If you said svn, where should I checkout from?
env.svnrepo = ''

# If you said git, where should I clone from and which branch should I checkout?
env.gitrepo = ''
env.gitbranch = 'master'

# These are the credentials for the wordpress. They should match your wp-config.php.
env.db_host = 'localhost'
env.db_name = env.project_name
env.db_wpuser_name = env.project_name
env.db_wpuser_pass = 'changeme' #make up something complicated for the password

# Super user name and pass for adding users and databases to mysql
env.db_root_user = "root"
env.db_root_pass = "root"

# This is the config file that will get installed on bootstrap
env.config_file = 'wp-config-mamp.php'

# Fix permissions throughout the deployment process. You may need to use this
# if perms are getting messed up.
env.fix_perms = False

# This defaults the run and sudo functions to local, so we don't have to duplicate
# code for local development and deployed servers.
env.sudo = local
env.run = local

# Where should I get Wordpress??
env.wp_tarball = "http://wordpress.org/latest.tar.gz"

"""
Environments
"""
def production():
    """
    Work on production environment
    """
    env.settings = 'production'
    env.hosts = ['example.com']
    env.user = ''
    env.path = ''
    env.wpdomain = 'example.com'
    env.db_root_user = 'wpcustomuser'
    env.db_root_pass = ''
    env.config_file = 'wp-config-production.php'
    env.db_host = 'db.example.com'
    env.db_name = 'wp_custom_database'
    check_env()

def staging():
    """
    Work on staging environment
    """
    env.settings = 'staging'
    env.hosts = ['staging.example.com']
    env.user = ''
    env.path = ''
    env.wpdomain = 'staging.example.com'
    env.db_root_user = 'wpsuperuser'
    env.db_root_pass = ''
    env.config_file = 'wp-config-staging.php'
    check_env()

"""
Commands - setup
"""
def git_clone_repo():
    """
    Do initial clone of the git repository.
    """
    with settings(warn_only=True):
        run('git clone %(gitrepo)s %(path)s' % env)

def git_checkout():
    """
    Pull the latest code on the specified branch.
    """
    with cd(env.path):
        if env.branch != 'master':
            with settings(warn_only=True):
                run('git checkout -b %(gitbranch)s origin/%(gitbranch)s' % env)
        run('git checkout %(gitbranch)s' % env)
        run('git pull origin %(gitbranch)s' % env)

def svn_checkout():
    """
    Checkout the site
    """
    env.svn_user = prompt('SVN Username: ')
    env.svn_pass = getpass('Enter SVN Password: ')
    
    with cd(env.path):
        run('svn co %(repo)s . --username %(svn_user)s --password %(svn_pass)s' % env)

"""
Commands - deployment
"""
def setup():
    """
    Setup the site
    """
    if env.strategy == 'git':
        git_clone()
        git_checkout()
    elif env.strategy == 'svn':
        svn_checkout()

    fix_perms()

def deploy():
    """
    Deploy new code to the site
    """
    if env.strategy == 'git':
        git_clone()
        git_checkout()
    elif env.strategy == 'svn':
        svn_checkout()

    fix_perms()

"""
Commands - data
"""
def bootstrap():
    print("\nStep 1: Database and basic Wordpress setup")

    with cd(env.path):
        env.run('cp -P %(config_file)s wp-config.php' % env)
    
    fix_perms()

    create_db()
    env.run('curl -s http://%(wpdomain)s/scripts/na-install.php' % env)
    
    print("\nStep 2: Setup plugins")
    
    env.run('curl -s http://%(wpdomain)s/scripts/na-setup-plugins.php' % env)
    
    print("\nStep 3: Cleanup, create blogs")

    env.run('curl -s http://%(wpdomain)s/scripts/na-postinstall.php' % env)
    
    if confirm("Create child blogs?"): create_blogs()

def create_db():
    if not env.db_root_pass:
        env.db_root_pass = getpass("Database password: ")

    env.run('mysqladmin --host=%(db_host)s --user=%(db_root_user)s --password=%(db_root_pass)s create %(db_name)s' % env)
    env.run('echo "GRANT ALL ON * TO \'%(db_wpuser_name)s\'@\'localhost\' IDENTIFIED BY \'%(db_wpuser_pass)s\';" | mysql --host=%(db_host)s --user=%(db_root_user)s --password=%(db_root_pass)s %(db_name)s' % env)

def load_db(dump_slug='dump'):
    env.dump_slug = dump_slug
    if not env.db_root_pass:
        env.db_root_pass = getpass("Database password: ")
    with cd(env.path):
        env.run("bzcat data/%(dump_slug)s.sql.bz2 |sed s/WPDEPLOYDOMAN/%(wpdomain)s/g |mysql --host=%(db_host)s --user=%(db_root_user)s --password=%(db_root_pass)s %(db_name)s" % env)

def dump_db(dump_slug='dump'):
    require('settings', provided_by=[staging, development, testing])
    env.dump_slug = dump_slug
    if not env.db_root_pass:
        env.db_root_pass = getpass("Database password: ")
    with cd(env.path):
        env.run("mysqldump --host=%(db_host)s --user=%(db_user)s --password=%(db_pass)s --lock-all-tables %(project_name)s |sed s/%(dev_url)s/WPDEPLOYDOMAN/g |bzip2 > data/%(dump_slug)s.sql.bz2" % env)

def destroy_db():
    if not env.db_root_pass:
        env.db_root_pass = getpass("Database password: ")

    with settings(warn_only=True):
        env.run('mysqladmin -f --host=%(db_host)s --user=%(db_root_user)s --password=%(db_root_pass)s drop %(project_name)s' % env)
        env.run('echo "DROP USER \'%(db_wpuser_name)s\'@\'localhost\';" | mysql --host=%(db_host)s --user=%(db_root_user)s --password=%(db_root_pass)s' % env)
    
def destroy_attachments():
    with cd(env.path):
        env.run('rm -rf wp-content/blogs.dir')

def reload_db(dump_slug='dump'):
    destroy_db()
    create_db()
    load_db(dump_slug)

def create_blogs():
    response = "Success"
    base_cmd = 'curl -s http://%(wpdomain)s/scripts/na-createblog.php' % env
    i=0
    while "Success" in response:
        response = env.run(base_cmd + '?new_blog_index=%s' % i)
        i+=1
        print(response)
    print("Created %s blogs" % str(i-1))

def fix_perms():
    if env.fix_perms:    
        env.sudo("chown -Rf %(apache_user)s:%(apache_group)s %(path)s; chmod -Rf ug+rw %(path)s;" % env)

def wrap_media():
    with cd(env.path):
        env.run('tar zcf data/media.tgz wp-content/blogs.dir/* wp-content/uploads/*')
    print('Wrapped up media.\n')

def unwrap_media():
    with cd(env.path):
        env.run('tar zxf data/media.tgz')
    print('Unwrapped media.\n')

def put_media():
    check_env()
    put('data/media.tgz','%(path)s/data/media.tgz' % env)
    print('Put media on server.\n')

def get_media():
    check_env()
    get('%(path)s/data/media.tgz' % env, 'data/media.tgz')
    print('Got media from the server.\n')

"""
Deaths, destroyers of worlds
"""
def shiva_the_destroyer():
    """
    Remove all directories, databases, etc. associated with the application.
    """
    try:
        check_env()
        env.run('rm -Rf %(path)s/* %(path)s/.*;' % env)
        destroy_db()
    except NameError, e:
        with settings(warn_only=True):
            env.run('rm .htaccess')
            env.run('rm wp-config.php')
        destroy_db()

"""
Utilities
"""
def check_env():
    require('settings', provided_by=[production, staging, development, testing])
    env.sudo = sudo
    env.run = run

def get_wordpress():
    print("Downloading and installing Wordpress...")
    with cd(env.path):
        env.run('curl -s %(wp_tarball)s | tar xzf - ' % env)
        env.run('mv wordpress/* .')
        env.run('rmdir wordpress')
    print("Done.")

def install_plugin(name, version='latest'):
    try:
        from lxml.html import parse
        from lxml.cssselect import CSSSelector
    except ImportError:
        print("I need lxml to do this")
        exit()

    print("Looking for %s..." % name)

    url = "http://wordpress.org/extend/plugins/%s/" % name
    p = parse("%sdownload/" % url)
    sel = CSSSelector('.block-content .unmarked-list a')
    dload_elems = sel(p)

    if not dload_elems:
        print("Can't find plugin %s" % name)
        exit()

    #first is latest
    if version == 'latest':
        plugin_zip = dload_elems[0].attrib['href']
        version = dload_elems[0].text
    else:
        plugin_zip = None
        for e in dload_elems:
            if e.text == 'version':
                plugin_zip = e.attrib['href']
                break

    if not plugin_zip:
        print("Can't find plugin %s" % name)
        exit()
    else:
        print("Found version %s of %s, installing..." % (version, name) )
        with cd(env.path + "/wp-content/plugins"):
            env.run('curl -s %s -o %s.%s.zip' % (plugin_zip, name, version) )
            env.run('unzip -n %s.%s.zip' % (name, version) )

        if raw_input("Read instructions for %s? [Y|n]" % name) in ("","Y"):
            subprocess.call(['open', url])
