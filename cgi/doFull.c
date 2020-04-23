/* $Id: doFull.c 1821 2008-01-31 21:16:56Z alberto $ */

#include <popt.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <unistd.h>
#include <pwd.h>
#include <errno.h>
#include <ctype.h>

#define SETUID_USER "monitor"
#define LOCAL_USER "monitor"
#define MAXSTRLEN 1024
#define RWNET_FULL_PATH "/home/monitor/rwatch/rwnet/rwnet-http-full"
#define RWNET_FULL_TIMEOUT "30"

uid_t getUidFromName(char *name);
void parseargs(int argc, const char **argv);
void verificar_status(int status);

/* parametros de linea de comando */
char *origen, *objetivo, *objeto;
char objetivo_calificado[MAXSTRLEN],objeto_calificado[MAXSTRLEN];


int
main(int argc, const char **argv)
{
    uid_t uid = getUidFromName(LOCAL_USER);
	pid_t pid;
	char origen_calificado[MAXSTRLEN];
	int status;
	int	filedes[2];
	int len;
	char *filename;
	int convs;
	
	parseargs(argc, argv);

	/*
	 * Debemos definir no solo el effective UID, sino tambien el "real UID",
	 * de manera que SSH no tenga problemas para encontrar la llave privada
	 * en el home del usuario.
	 */
	if (setreuid(uid, uid) != 0)
	{
		perror("setreuid");
		exit(EXIT_FAILURE);
	}

	snprintf(origen_calificado, MAXSTRLEN, "%s@%s", SETUID_USER, origen);

	/*
	 * Este programa funciona con dos procesos: un proceso hijo se abre
	 * para ejecutar el rwnet-http-full, el cual retorna un nombre de
	 * archivo.  El proceso padre recibe ese nombre de archivo y lo copia
	 * a la salida estandar.  Cuando eso esta hecho, lo borra en el
	 * servidor remoto.
	 *
	 * La comunicacion entre ambos se lleva a cabo a traves de pipes.
	 */
	if (pipe(filedes) != 0)
	{
		perror("pipe");
		exit(EXIT_FAILURE);
	}

	pid = fork();
	if (pid < 0)
	{
		perror("fork");
		exit(EXIT_FAILURE);
	}

	if (pid == 0)
	{
		/*
		 * El proceso hijo.  Aca invocamos SSH que ejecuta el http-full y
		 * retorna el nombre de archivo que debemos bajar.  La salida estandar
		 * debe reabrirse usando el extremo de escritura del pipe.
		 */
		if (dup2(filedes[1], STDOUT_FILENO) < 0)
		{
			perror("dup2");
			exit(EXIT_FAILURE);
		}
		/*
		 * FIXME -- habria que hacer alguna especie de chequeo a las variables
		 * origen, objetivo y objeto antes de invocar execl().  El ultimo
		 * parametro indica que se debe escapar la URL.
		 */
		sprintf(objetivo_calificado,"'%s'",objetivo);
		sprintf(objeto_calificado,"'%s'",objeto);
		execl("/usr/bin/ssh",
				"/usr/bin/ssh", origen_calificado, "-l", "monitor",
				RWNET_FULL_PATH, objetivo_calificado, objeto_calificado, RWNET_FULL_TIMEOUT, "2",
				(char *)NULL);

		/* execl no debe retornar */
		printf("execl failed");
		exit(EXIT_FAILURE);
	}

	/*
	 * El proceso padre.  Esperamos que el hijo termine, y verificamos su
	 * codigo de retorno.  De ser OK, leemos el nombre de archivo a obtener
	 * desde el pipe.
	 */
	waitpid(pid, &status, 0);
	verificar_status(status);

	if ((filename = malloc(MAXSTRLEN + 1)) == NULL)
	{
		perror("malloc");
		exit(EXIT_FAILURE);
	}
	if ((len = read(filedes[0], filename, MAXSTRLEN)) < 0)
	{
		perror("read");
		exit(EXIT_FAILURE);
	}

	/* Quitar basura (newlines, etc) al final, de haberla */
	while (!isprint(filename[len]))
		filename[len--] = '\0';

	/* rwnet-http-full retorna "<codigo> <archivo>" */
	convs = sscanf(filename, "%d %s", &status, filename);
	if (convs != 2)
	{
		fprintf(stderr, "no se encontraron los tokens buscados");
		exit(EXIT_FAILURE);
	}
	/* la variable "filename" tiene el nombre de archivo que deseamos */

	/*
	 * Ahora hacemos otro fork para traernos el archivo y escupirlo en la
	 * salida.
	 */

	pid = fork();
	if (pid < 0)
	{
		perror("fork");
		exit(EXIT_FAILURE);
	}

	if (pid == 0)
	{
		/*
		 * El proceso hijo.  Aca invocamos SSH que escribe a la salida
		 * estandar el archivo que fue creado por rwnet-http-full.
		 */
		if (dup2(STDOUT_FILENO, filedes[1]) < 0)
		{
			perror("dup2");
			exit(EXIT_FAILURE);
		}
		execl("/usr/bin/ssh",
				"/usr/bin/ssh", origen_calificado,
				"/bin/cat", filename,
				(char *)NULL);

		/* execl no debe retornar */
		printf("execl failed");
		exit(EXIT_FAILURE);
	}

	/*
	 * El proceso padre.  Ponemos la entrada de lectura del pipe hacia
	 * la salida estandar, para que lo que lea el proceso hijo se escriba
	 * hacia afuera.
	 */
	if (dup2(filedes[0], STDOUT_FILENO) < 0)
	{
		perror("dup2");
		exit(EXIT_FAILURE);
	}
	waitpid(pid, &status, 0);
	verificar_status(status);

	/* Un ultimo execl para eliminar el archivo, y con eso nos vamos */
	execl("/usr/bin/ssh",
			"/usr/bin/ssh", origen_calificado,
			"/bin/rm", "-f", filename,
			(char *)NULL);

	fprintf(stderr, "execl failed");
	exit(EXIT_FAILURE);
}

void
parseargs(int argc, const char **argv) {
	poptContext optCon;

	struct poptOption optionsTable[] = {
		{ "origen", 'o', POPT_ARG_STRING, &origen, 0, "servidor de origen de pings", "servidor"},
		{ "objetivo", 0, POPT_ARG_STRING, &objetivo, 0, "objetivo para pagina completa", "objetivo"},
		{ "objeto", 0, POPT_ARG_STRING, &objeto, 0, "objeto para pagina completa", "objeto"},
		POPT_AUTOHELP
		POPT_TABLEEND
	};
	optCon = poptGetContext(NULL, argc, argv, optionsTable, 0);
	/*
	 * Si hubiera que ampliar las opciones de la linea de comandos, esto se
	 * deberia transformar en un ciclo while.
	 */
	poptGetNextOpt(optCon);

	if (objetivo == NULL || strlen(objetivo) == 0)
	{
		poptPrintHelp(optCon, stderr, 0);
		exit(EXIT_FAILURE);
	}
	if (objeto == NULL || strlen(objeto) == 0)
	{
		poptPrintHelp(optCon, stderr, 0);
		exit(EXIT_FAILURE);
	}
	if (origen == NULL || strlen(origen) == 0)
	{
		poptPrintHelp(optCon, stderr, 0);
		exit(EXIT_FAILURE);
	}
}

uid_t
getUidFromName(char *name)
{
	struct passwd *pwd;

	if (name == NULL)
	{
		fprintf(stderr, "name es nulo, error\n");
		exit(EXIT_FAILURE);
	}

	errno = 0;
	pwd = getpwnam(name);
	if (errno != 0)
	{
		perror("getpwnam");
		exit(EXIT_FAILURE);
	}
	if (pwd == NULL)
	{
		fprintf(stderr, "no se encuentra usuario %s\n", name);
		exit(EXIT_FAILURE);
	}

	return pwd->pw_uid;
}

void verificar_status(int status) {
	if (!WIFEXITED(status))
	{
		fprintf(stderr, "child did not exit");
		exit(EXIT_FAILURE);
	}
	if (WEXITSTATUS(status) != EXIT_SUCCESS)
	{
		fprintf(stderr, "child status is %d, not success", WEXITSTATUS(status));
		exit(EXIT_FAILURE);
	}
}

/*
 * vim: sw=4 ts=4
 */
