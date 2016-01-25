module.exports = function(grunt) {

	require('matchdep').filterDev('grunt-*').forEach( grunt.loadNpmTasks );

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		sass: {
			colors: {
				options: {
					style: 'compact',
					noCache: true,
					sourcemap: false
				},
				expand: true,
				cwd: 'assets',
				dest: 'assets',
				ext: '.css',
				src: [
					'colors.scss'
				]
			}
		},

		rtlcss: {
			colors: {
				expand: true,
				cwd: 'assets',
				dest: 'assets',
				ext: '-rtl.css',
				src: [
					'colors.css'
				]
			}
		},

		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1
			},
			target: {
				files: [{
					expand: true,
					cwd: 'assets/',
					src: ['*.css', '!*.min.css'],
					dest: 'assets/',
					ext: '.min.css'
				}]
			}
		},

		watch: {
			sass: {
				files: ['assets/*.scss', ],
				tasks: ['sass:colors']
			}
		}

	});

	// Default task(s).
	grunt.registerTask('default', ['sass', 'rtlcss', 'cssmin']);

};
