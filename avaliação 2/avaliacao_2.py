# =========================
# Exercício 1: Soma de dois números
# =========================
n1 = float(input("Ex1 - Digite o primeiro número: "))
n2 = float(input("Ex1 - Digite o segundo número: "))
print("Ex1 - A soma é:", n1 + n2)

# =========================
# Exercício 2: Área de um círculo (πr²)
# =========================
import math
raio = float(input("\nEx2 - Digite o raio do círculo: "))
print("Ex2 - A área do círculo é:", math.pi * raio**2)

# =========================
# Exercício 3: Maior entre dois números
# =========================
a = float(input("\nEx3 - Digite o primeiro número: "))
b = float(input("Ex3 - Digite o segundo número: "))
if a > b:
    print("Ex3 - O maior número é:", a)
elif b > a:
    print("Ex3 - O maior número é:", b)
else:
    print("Ex3 - Os números são iguais.")

# =========================
# Exercício 4: Fatorial
# =========================
num = int(input("\nEx4 - Digite um número para calcular o fatorial: "))
fatorial = 1
for i in range(1, num + 1):
    fatorial *= i
print(f"Ex4 - O fatorial de {num} é {fatorial}")

# =========================
# Exercício 5: Calculadora básica
# =========================
x = float(input("\nEx5 - Digite o primeiro número: "))
y = float(input("Ex5 - Digite o segundo número: "))
op = input("Ex5 - Digite a operação (+, -, *, /): ")
if op == "+":
    print("Ex5 - Resultado:", x + y)
elif op == "-":
    print("Ex5 - Resultado:", x - y)
elif op == "*":
    print("Ex5 - Resultado:", x * y)
elif op == "/":
    if y != 0:
        print("Ex5 - Resultado:", x / y)
    else:
        print("Ex5 - Erro: divisão por zero")
else:
    print("Ex5 - Operação inválida.")

# =========================
# Exercício 6: Aumento salarial
# =========================
salario = float(input("\nEx6 - Digite o salário: "))
if salario > 2000:
    aumento = salario * 0.125
else:
    aumento = salario * 0.1775
print("Ex6 - Novo salário:", salario + aumento)

# =========================
# Exercício 7: Classificação por idade
# =========================
from datetime import datetime
nome = input("\nEx7 - Digite seu nome: ")
ano_nasc = int(input("Ex7 - Ano de nascimento: "))
idade = datetime.now().year - ano_nasc
if idade <= 11:
    classe = "Criança"
elif idade <= 18:
    classe = "Adolescente"
elif idade <= 24:
    classe = "Jovem"
elif idade <= 40:
    classe = "Adulto"
elif idade <= 60:
    classe = "Meia Idade"
else:
    classe = "Idoso"
print(f"Ex7 - {nome} é classificado como: {classe}")

# =========================
# Exercício 8: Média de notas
# =========================
n1 = float(input("\nEx8 - Nota 1: "))
n2 = float(input("Ex8 - Nota 2: "))
n3 = float(input("Ex8 - Nota 3: "))
media = (n1 + n2 + n3) / 3
if media >= 7:
    status = "Aprovado"
elif media >= 5:
    status = "Recuperação"
else:
    status = "Reprovado"
print(f"Ex8 - Média: {media:.2f} - {status}")

# =========================
# Exercício 9: Números pares até um número
# =========================
limite = int(input("\nEx9 - Digite um número: "))
print("Ex9 - Números pares até o limite:")
for i in range(0, limite + 1, 2):
    print(i, end=" ")
print()

# =========================
# Exercício 10: Soma de ímpares até um número
# =========================
num = int(input("\nEx10 - Digite um número: "))
soma_impares = sum(i for i in range(1, num + 1, 2))
print("Ex10 - Soma dos ímpares:", soma_impares)
