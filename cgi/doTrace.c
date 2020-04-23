/* $Id: doTrace.c 1377 2006-12-13 20:12:08Z alberto $ */

#include <popt.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/types.h>
#include <unistd.h>
#include <pwd.h>
#include <errno.h>

#define SETUID_USER "monitor"
#define LOCAL_USER "monitor"
#define MAXSTRLEN 1024

uid_t getUidFromName(char *name);
void parseargs(int argc, const char **argv);

/* parametros de linea de comando */
char *origen, *destino;


int
main(int argc, const char **argv) {
    uid_t uid = getUidFromName(LOCAL_USER);
	char origen_calificado[MAXSTRLEN];
	
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

	/*  snprintf(origen_calificado, MAXSTRLEN, "%s@%s", SETUID_USER, origen); */

	 snprintf(origen_calificado, MAXSTRLEN, "%s@%s", SETUID_USER, origen);

	/* FIXME -- habria que hacer alguna especie de chequeo a las variables
	 * origen y destino antes de invocar execl().
	 */
	execl("/usr/bin/ssh", "/usr/bin/ssh", origen_calificado, "-l", "monitor",  "/usr/bin/tcptraceroute", destino, "-q1", "-w2", (char *)NULL);

	/* execl no debe retornar */

	printf("execl failed");
    exit(EXIT_FAILURE);
}

void
parseargs(int argc, const char **argv) {
	poptContext optCon;

	struct poptOption optionsTable[] = {
		{ "origen", 'o', POPT_ARG_STRING, &origen, 0, "servidor de origen de traceroute", "servidor"},
		{ "destino",'d', POPT_ARG_STRING, &destino, 0, "host de destino de traceroute", "host"},
		POPT_AUTOHELP
		POPT_TABLEEND
	};
	optCon = poptGetContext(NULL, argc, argv, optionsTable, 0);
	/*
	 * Si hubiera que ampliar las opciones de la linea de comandos, esto se
	 * deberia transformar en un ciclo while.
	 */
	poptGetNextOpt(optCon);

	if (destino == NULL || strlen(destino) == 0)
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

/*
 * vim: sw=4 ts=4
 */
