# ? NAMING CONVENTIONS ON PERSO :
'my'<Class>' : class depending only on php or other 'my' classes

'ow'<Class>' : classes depending also on sf fw classes

# include in project
as a rule, include needed classes :
  - online : as myClassesPlugin
owMyUserExtPlugin  - local  : idem but with autoloading path set to central local dir 'perso_php'=======
'ow'<Class>' : classes depending also on sf fw classes

# INSTALL :
* local install : (we want to share same files for all apps !)
  * helper functions : add 'helper' dirs to include_path (in ProjectConfiguration.class.php for example)

  * classes          : add dir to autoload => autoload.yml at project level (@see 'README' dir for pre-formatted file ;-)

* online install :
owMyUserExtPlugin  * classes & helpers are loaded as a plugin : 'myClassesPlugin' for example
