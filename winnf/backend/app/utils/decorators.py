from functools import wraps
from flask import jsonify
from flask_jwt_extended import verify_jwt_in_request, get_jwt


def role_required(*roles):
    """
    Decorator that requires the current JWT to belong to one of the given roles.

    Usage:
        @role_required("admin")
        @role_required("admin", "produtor")
    """
    def decorator(fn):
        @wraps(fn)
        def wrapper(*args, **kwargs):
            verify_jwt_in_request()
            claims = get_jwt()
            user_role = claims.get("role")
            if user_role not in roles:
                return jsonify(
                    {
                        "success": False,
                        "message": "Acesso negado: permissão insuficiente.",
                    }
                ), 403
            return fn(*args, **kwargs)
        return wrapper
    return decorator


def paginate_query(query, page: int, per_page: int) -> dict:
    """
    Helper that paginates a SQLAlchemy query and returns a standard dict.
    """
    per_page = min(per_page, 100)  # cap to avoid abuse
    paginated = query.paginate(page=page, per_page=per_page, error_out=False)
    return {
        "items": paginated.items,
        "total": paginated.total,
        "page": paginated.page,
        "pages": paginated.pages,
        "per_page": paginated.per_page,
    }
