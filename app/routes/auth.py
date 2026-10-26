from flask import Blueprint, redirect, request

auth_bp = Blueprint('auth', __name__)

@auth_bp.route('/login', methods=['GET', 'POST'])
def login():
    return redirect('http://127.0.0.1:8000/login')

@auth_bp.route('/register', methods=['GET', 'POST'])
def register():
    return redirect('http://127.0.0.1:8000/register')

@auth_bp.route('/logout', methods=['GET', 'POST'])
def logout():
    return redirect('http://127.0.0.1:8000/logout')
