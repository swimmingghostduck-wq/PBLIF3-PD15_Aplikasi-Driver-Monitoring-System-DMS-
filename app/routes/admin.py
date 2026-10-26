from flask import Blueprint, redirect, request

admin_bp = Blueprint('admin', __name__)

@admin_bp.route('', defaults={'path': ''}, methods=['GET', 'POST'])
@admin_bp.route('/', defaults={'path': ''}, methods=['GET', 'POST'])
@admin_bp.route('/<path:path>', methods=['GET', 'POST'])
def catch_all_admin(path):
    target = f"http://127.0.0.1:8000/admin/{path}" if path else "http://127.0.0.1:8000/admin/dashboard"
    return redirect(target)
