// Copyright (c) 2007  INRIA Sophia-Antipolis (France), INRIA Lorraine LORIA.
// All rights reserved.
//
// This file is part of CGAL (www.cgal.org); you may redistribute it under
// the terms of the Q Public License version 1.0.
// See the file LICENSE.QPL distributed with CGAL.
//
// Licensees holding a valid commercial license may use this file in
// accordance with the commercial license agreement provided with the software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING THE
// WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.
//
// $URL$
// $Id$
//
// Author(s)     : Marc Pouget and Fr�d�ric Cazals
#ifndef CGAL_LAPACK_H
#define CGAL_LAPACK_H

extern "C" {
  int dgelss(int *m, int *n, int *nrhs,
                    double *a, int *lda, double *b, int *ldb, double *
                    s, double *rcond, int *rank, double *work, int *lwork,
                    int *info);
  int dgelss_(int *m, int *n, int *nrhs,
                    double *a, int *lda, double *b, int *ldb, double *
                    s, double *rcond, int *rank, double *work, int *lwork,
                    int *info);
}

namespace CGAL { namespace LAPACK {

inline
int dgelss(int *m, int *n, int *nrhs,
       double *a, int *lda, double *b, int *ldb, double *
       s, double *rcond, int *rank, double *work, int *lwork,
       int *info)
{
#ifdef CGAL_USE_F2C
  return ::dgelss_(m, n, nrhs, a, lda, b, ldb, s, rcond, rank, work, lwork, info);
#else
  return ::dgelss(m, n, nrhs, a, lda, b, ldb, s, rcond, rank, work, lwork, info);
#endif
}

} }


namespace CGAL {
  
////////////////////////class Lapack_vector/////////////////////
class Lapack_vector{
  typedef double FT;
 protected:
  FT* m_vector;
  size_t nb_elts;
 public:
  //contructor
  // initializes all the elements of the vector to zero.
  Lapack_vector(size_t n) { 
    m_vector = (FT*) calloc (n, sizeof(FT)); 
    nb_elts = n;
  }
  
  size_t size() {return nb_elts;}
  //data access
  const FT* vector() const { return m_vector;}
  FT* vector() { return m_vector; }

  FT operator()(size_t i) {return m_vector[i];}
  void set(size_t i, const FT value) { m_vector[i] = value; }
}; 


////////////////////////class Lapack_matrix/////////////////////
//in clapack, matrices are one-dimensional arrays and elements are
//column-major ordered. This class is a wrapper defining set and get
//in the usual way with line and column indices.
class Lapack_matrix{
  typedef double FT;
protected:
  FT* m_matrix;
  size_t nb_rows;
  size_t nb_columns;
public:
  //contructor
  // initializes all the elements of the matrix to zero.
  Lapack_matrix(size_t n1, size_t n2) { 
    m_matrix = (FT*) calloc (n1*n2, sizeof(FT)); 
    nb_rows = n1;
    nb_columns = n2;
  }
  
  size_t number_of_rows() {return nb_rows;}
  size_t number_of_columns() {return nb_columns;}

  //access
  const FT* matrix() const { return m_matrix; }
  FT* matrix() { return m_matrix; }

  void set(size_t i, size_t j, const FT value) { m_matrix[j*nb_rows+i] = value; }
  FT operator()(size_t i, size_t j) { return m_matrix[j*nb_rows+i]; }
}; 

////////////////////////class Lapack_svd/////////////////////
class Lapack_svd{
public:
  typedef double FT;
  typedef Lapack_vector Vector;
  typedef Lapack_matrix Matrix;
  //solve MX=B using SVD and return the condition number of M
  //The solution is stored in B
  static
    FT solve(Matrix& M, Vector& B);
};

 Lapack_svd::FT Lapack_svd::solve(Matrix& M, Vector& B)
{
  int m = M.number_of_rows(),
    n = M.number_of_columns(),
    nrhs = 1,
    lda = m,
    ldb = m,
    rank,
    lwork = 5*m,
    info;
  //c style
  FT* sing_values = (FT*) malloc(n*sizeof(FT));
  FT* work = (FT*) malloc(lwork*sizeof(FT));

  FT rcond = -1;

  LAPACK::dgelss(&m, &n, &nrhs, M.matrix(), &lda, B.vector(), &ldb, sing_values, 
	  &rcond, &rank, work, &lwork, &info);
  assert(info==0);

  FT cond_nb = sing_values[0]/sing_values[n-1];
  
  //clean up 
  free(sing_values);
  free(work);

  return cond_nb;
}

} // namespace CGAL

#endif // CGAL_LAPACK_H
