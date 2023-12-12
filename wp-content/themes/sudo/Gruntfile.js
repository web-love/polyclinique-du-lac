module.exports = function(grunt) {

  grunt.initConfig({
    sass: {                              // Nom de la tâche
      dist: {                            // Nom de la sous-tâche
        options: {                       // Options
          style: 'compressed'
        },
        files: {                         // Liste des fichiers
          'styles/style.min.css': 'lib/sass/manifest.scss',       // 'destination': 'source'
		  'patch/patch-touch.min.css': 'lib/sass/_patch/patch-touch.scss',
		  'patch/patch-above-fold.min.css': 'lib/sass/above-fold.scss'
        }
      }
    },
    concat: {
      options: {
        separator: ';'
      },
      dist: {
        src: ['lib/js/*/*.js', 'lib/js/*.js', '!lib/js/*/disabled.*', '!lib/js/disabled.*'],
        dest: 'scripts/library.js'
      }
    },
    uglify: {
      options: {
        separator: ';'
      },
      dist: {
        src: ['scripts/library.js'],
        dest: 'scripts/library.min.js'
      },
      coffee:{
        src: ['scripts/coffee.js'],
        dest: 'scripts/coffee.min.js'
      }
    },
    coffee: {
      compileJoined: {
        options: {
          join: true
        },
        files: {
          'lib/js/coffee.js': 'lib/coffee/*.coffee' // 1:1 compile, identical output to join = false
        }
      }
    },
    watch: {
      scripts: {
        files: ['lib/js/*.js', 'lib/js/*/*.js'], // tous les fichiers JavaScript de n'importe quel dossier
        tasks: ['concat:dist', 'uglify:dist', 'cachebreaker:dev']
      },
      styles: {
        files: ['lib/sass/*.scss','lib/sass/*/*.scss'], // tous les fichiers Sass de n'importe quel dossier
        tasks: ['sass:dist', 'cachebreaker:dev']
      },
      expresso:{
        files: ['lib/coffee/*.coffee'], // tous les fichiers Sass de n'importe quel dossier
        tasks: ['coffee:compileJoined', 'uglify:coffee', 'cachebreaker:dev']
      },
	  html:{
		files: ['lib/views/_modules/*.html', 'lib/views/*.html'], // tous les fichiers Sass de n'importe quel dossier
        tasks: ['includes', 'cachebreaker:dev']
	  },
    },
	includes: {
	  files: {
		src: ['lib/views/*.html'], // Source files
		dest: '', // Destination directory
		flatten: true,
		cwd: '.',
		options: {
		  silent: true,
		  //banner: '<!-- I am a banner <% includes.files.dest %> -->'
		}
	  }
	},
	cachebreaker: {
		dev: {
			options: {
				match: [
					{
						// Pattern    // File to hash 
						'style.min.css':  'styles/style.min.css',
						'library.min.js': 'scripts/library.min.js',
						'patch-touch.min.css': 'patch/patch-touch.min.css',
						'patch-above-fold.min.css': 'patch/patch-above-fold.min.css'
					}
				],
				replacement: 'md5'
			},
			files: {
				src: ['*.html', '*.php']
			}
		}
	},
	reload: {
        port: 52681,
        proxy: {
            host: '127.0.0.1',
        }
    },
  })
  
  grunt.loadNpmTasks('grunt-includes');
  grunt.loadNpmTasks('grunt-reload');
  grunt.loadNpmTasks('grunt-cache-breaker');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-coffee');

  grunt.registerTask('default', ['sass:dist', 'concat:dist', 'uglify:dist', 'cachebreaker:dev', 'reload'])
}