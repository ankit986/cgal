@! ============================================================================
@! The CGAL Project
@! Implementation: 2D Smallest Enclosing Circle
@! ----------------------------------------------------------------------------
@! file  : Library/web/Min_circle_2.aw
@! author: Bernd G�rtner, Sven Sch�nherr (sven@inf.fu-berlin.de)
@! ----------------------------------------------------------------------------
@! $Revision$
@! $Date$
@! ============================================================================
 
@p maximum_input_line_length = 180
@p maximum_output_line_length = 180

@documentclass[twoside]{article}
@usepackage[latin1]{inputenc}
@usepackage{a4wide2}
@usepackage{amssymb}
@usepackage{cc_manual}
@article

\input{cprog.sty}
\setlength{\parskip}{0.6ex}

@! LaTeX macros
\newenvironment{pseudocode}[1]%
  {\vspace*{-0.5\baselineskip} \upshape \begin{tabbing}
     99 \= bla \= bla \= bla \= bla \= bla \= bla \= bla \= bla \kill
     #1 \+ \\}%
  {\end{tabbing}}
\newcommand{\keyword}[1]{\texttt{#1}}
\newcommand{\IF}{\keyword{IF} }
\newcommand{\THEN}{\keyword{THEN} \+ \\}
\newcommand{\ELSE}{\< \keyword{ELSE} \\}
\newcommand{\END}{\< \keyword{END} \- \\ }
\newcommand{\OR}{\keyword{OR} }
\newcommand{\FOR}{\keyword{FOR} }
\newcommand{\TO}{\keyword{TO} }
\newcommand{\DO}{\keyword{DO} \+ \\}
\newcommand{\RETURN}{\keyword{RETURN} }

\newcommand{\mc}{\texttt{mc}}

@! ============================================================================
@! Title
@! ============================================================================

\RCSdef{\rcsrevision}{$Revision$}
\RCSdefDate{\rcsdate}{$Date$}

@t vskip 5 mm
@t title titlefont centre "CGAL -- 2D Smallest Enclosing Circle*"
@t vskip 1 mm
@t title smalltitlefont centre "Implementation Documentation"
@t vskip 5 mm
@t title smalltitlefont centre "Bernd G�rtner and Sven Sch�nherr"
\smallskip
\centerline{\rcsrevision\ , \rcsdate}
@t vskip 1 mm

\renewcommand{\thefootnote}{\fnsymbol{footnote}}
\footnotetext[1]{This work was supported by the ESPRIT IV LTR Project
  No.~21957 (CGAL).}

@! ============================================================================
@! Introduction and Contents
@! ============================================================================

\section*{Introduction}

We provide an implementation of an optimisation algorithm for computing
the smallest (w.r.t.\ area) enclosing circle of a finite point set $P$ in
the plane. The class template \ccc{CGAL_Min_circle_2<I>} is implemented
as a semi-dynamic data structure, thus allowing to insert points while
maintaining the smallest enclosing circle. It is parameterized with an
interface class, that defines the interface between the optimisation
algorithm and the primitives it uses.

This document is organized as follows. The algorithm is described in
Section~1. Section~2 contains the specification as it appears in the
CGAL Reference Manual. Section~3 gives the implementation. In
Section~4 we provide a test program which performs some correctness
checks. Finally the product files are created in Section~5.

\tableofcontents

@! ============================================================================
@! The Algorithm
@! ============================================================================

\setlength{\parskip}{0.8ex}
\clearpage
\section{The Algorithm} \label{sec:algo}

The implementation is based on an algorithm by Welzl~\cite{w-sedbe-91a},
which we shortly describe now. The smallest (w.r.t.\ area) enclosing
circle of a finite point set $P$ in the plane, denoted by $mc(P)$, is
built up incrementally, adding one point after another. Assume $mc(P)$
has been constructed, and we would like to obtain $mc(P \cup \{p\})$, $p$
some new point. There are two cases: if $p$ already lies inside $mc(P)$,
then $mc(P \cup \{p\}) = mc(P)$. Otherwise $p$ must lie on the boundary
of $mc(P \cup \{p\})$ (this is proved in~\cite{w-sedbe-91a} and not hard
to see), so we need to compute $mc(P,\{p\})$, the smallest circle
enclosing $P$ with $p$ on the boundary. This is recursively done in the
same manner. In general, for point sets $P$,$B$, define $mc(P,B)$ as the
smallest circle enclosing $P$ that has the points of $B$ on the boundary
(if defined). Although the algorithm finally delivers a circle
$mc(P,\emptyset)$, it internally deals with circles that have a possibly
nonempty set $B$. Here is the pseudocode of Welzl's method.  To compute
$mc(P)$, it is called with the pair $(P,\emptyset)$, assuming that
$P=\{p_1,\ldots,p_n\}$ is stored in a linked list.

\begin{pseudocode}{$\mc(P,B)$:}
  $mc := \mc(\emptyset,B)$ \\
  \IF $|B| = 3$ \keyword{THEN} \RETURN $mc$ \\
  \FOR $i := 1$ \TO $n$ \DO
      \IF $p_i \not\in mc$ \THEN
          $mc := \mc(\{p_1,\ldots,p_{i-1}\}, B \cup \{p_i\})$ \\
          move $p_i$ to the front of $P$ \\
      \END
  \END
  \RETURN $mc$ \\
\end{pseudocode}

Note the following: (a) $|B|$ is always bounded by 3, thus the
computation of $mc(\emptyset,B)$ is easy. In our implementation, it is
done by the private member function \ccc{compute_circle}. (b) One can
check that the method maintains the invariant `$mc(P,B)$ exists'. This
justifies termination if $|B| = 3$, because then $mc(P,B)$ must be the
unique circle with the points of $B$ on the boundary, and $mc(P,B)$
exists if and only if this circle contains the points of $P$. Thus, no
subsequent in-circle tests are necessary anymore (for details
see~\cite{w-sedbe-91a}). (c) points which are found to lie outside the
current circle $mc$ are considered `important' and are moved to the front
of the linked list that stores $P$. This is crucial for the method's
efficiency.

It can also be advisable to bring $P$ into random order before
computation starts. There are `bad' insertion orders which cause the
method to be very slow -- random shuffling gives these orders a very
small probability.

@! ============================================================================
@! Specification
@! ============================================================================

\clearpage
\section{Specification}

{\em Note:} Below some references are undefined, they refer to sections
in the \cgal\ Reference Manual.

\renewcommand{\ccSection}{\ccSubsection}
\renewcommand{\ccFont}{\tt}
\renewcommand{\ccEndFont}{}
\ccSetThreeColumns{Support_point_iterator}{Circle;}{}
\ccPropagateThreeToTwoColumns
\input{../spec/Min_circle_2.tex}

@! ============================================================================
@! Implementation
@! ============================================================================

\clearpage
\section{Implementation}

First, we declare the class template \ccc{CGAL_Min_circle_2}.

@macro<Min_circle_2 declaration> = @begin
    template < class I >
    class CGAL_Min_circle_2;
@end

The actual work of the algorithm is done in the private member
functions \ccc{mc} and \ccc{compute_circle}. The former directly
realizes the pseudocode of $\mc(P,B)$, the latter solves the basic
case $\mc(\emptyset,B)$, see Section~\ref{sec:algo}.

{\em Workaround:} The GNU compiler (g++ 2.7.*) does not accept types
with scope operator as argument type or return type in class template
member functions. Therefore, all member functions are implemented in
the class interface.

The class interface looks as follows.

@macro <Min_circle_2 interface> = @begin
    template < class _I >
    class CGAL_Min_circle_2 {
      public:
	@<Min_circle_2 public interface>

      private:
	// private data members
	@<Min_circle_2 private data members>

	// copying and assignment not allowed!
	CGAL_Min_circle_2( CGAL_Min_circle_2<_I> const&);
	CGAL_Min_circle_2<_I>& operator = ( CGAL_Min_circle_2<_I> const&);

    @<dividing line>

    // Class implementation
    // ====================

      public:
	// Access functions and predicates
	// -------------------------------
	@<Min_circle_2 access functions `number_of_...'>

	@<Min_circle_2 predicates `is_...'>

	@<Min_circle_2 access functions>

	@<Min_circle_2 predicates>

      private:
	// Privat member functions
	// -----------------------
	@<Min_circle_2 private member function `compute_circle'>

	@<Min_circle_2 private member function `mc'>

      public:
	// Constructors
	// ------------
	@<Min_circle_2 constructors>

	// Destructor
	// ----------
	@<Min_circle_2 destructor>

	// Modifiers
	// ---------
	@<Min_circle_2 modifiers>

	// Validity check
	// --------------
	@<Min_circle_2 validity check>

	// I/O
	// ---
	@<Min_circle_2 I/O operators>
    };
@end   

@! ----------------------------------------------------------------------------
\subsection{Public Interface}

The functionality is described and documented in the specification
section, so we do not comment on it here.

@macro <Min_circle_2 public interface> = @begin
    // types
    typedef           _I                           I;
    typedef typename  _I::Point                    Point;
    typedef typename  _I::Distance		   Distance;
    typedef typename  _I::Circle                   Circle;
    typedef typename  list<Point>::const_iterator  Point_iterator;
    typedef           const Point *                Support_point_iterator;

    /**************************************************************************
    WORKAROUND: The GNU compiler (g++ 2.7.*) does not accept types with
    scope operator as argument type or return type in class template
    member functions. Therefore, all member functions are implemented in
    the class interface.

    // creation
    CGAL_Min_circle_2( I const& i = I());
    CGAL_Min_circle_2( Point const& p,
		       I const& i = I());
    CGAL_Min_circle_2( Point const& p,
                       Point const& q,
		       I const& i = I());
    CGAL_Min_circle_2( Point const& p1,
                       Point const& p2,
                       Point const& p3,
		       I const& i = I());
    CGAL_Min_circle_2( const Point* first,
                       const Point* last,
                       bool randomize = false,
                       CGAL_Random& random = CGAL_random,
		       I const& i = I());
    CGAL_Min_circle_2( list<Point>::const_iterator first,
                       list<Point>::const_iterator last,
                       bool randomize = false,
                       CGAL_Random& random = CGAL_random,
		       I const& i = I());
    ~CGAL_Min_circle_2( );

    // access functions
    int  number_of_points        ( ) const;
    int  number_of_support_points( ) const;

    Point_iterator  points_begin( ) const;
    Point_iterator  points_end  ( ) const;

    Support_point_iterator  support_points_begin( ) const;
    Support_point_iterator  support_points_end  ( ) const;

    Point const&  support_point( int i) const;

    Circle  circle( ) const;

    // predicates
    CGAL_Bounded_side  bounded_side( Point const& p) const;
    bool  has_on_bounded_side      ( Point const& p) const;
    bool  has_on_boundary          ( Point const& p) const;
    bool  has_on_unbounded_side    ( Point const& p) const;

    bool  is_empty     ( ) const;
    bool  is_degenerate( ) const;

    // modifiers
    void  insert ( Point const& p);

    // validity check
    bool  is_valid( bool verbose = false, int level = 0) const;

    // I/O
    friend ostream& operator << ( ostream& os, CGAL_Min_circle_2<I> const& mc);
    friend istream& operator >> ( istream& is, CGAL_Min_circle_2<I>      & mc);
    **************************************************************************/
@end

@! ----------------------------------------------------------------------------
\subsection{Private Data Members}

First, the interface class object is stored.

@macro <Min_circle_2 private data members> += @begin
    I            ico;				// interface class object
@end

The points of $P$ are internally stored as a linked list that allows to
bring points to the front of the list in constant time. We use the
sequence container \ccc{list} from STL~\cite{sl-stl-95}.

@macro <Min_circle_2 private data members> += @begin
    list<Point>  points;			// doubly linked list of points
@end

The support set $S$ of at most three support points is stored in an
array \ccc{support_points}, the actual number of support points is
given by \ccc{n_support_points}. During the computations, the set of
support points coincides with the set $B$ appearing in the pseudocode
for $\mc(P,B)$, see Section~\ref{sec:algo}.

{\em Workaround:} The array of support points is allocated dynamically,
because the SGI compiler (mipspro CC 7.1) does not accept a static
array here.

@macro <Min_circle_2 private data members> += @begin
    int          n_support_points;		// number of support points
    Point*       support_points;		// array of support points
@end

Finally, an actual circle is stored in \ccc{center} and
\ccc{squared_radius}, by the end of computation equal to
$mc(P)$. During computation, this circle equals the circle $mc$
appearing in the pseudocode for $\mc(P,B)$, see
Section~\ref{sec:algo}. An empty circle is represented by a negative
squared radius.

@macro <Min_circle_2 private data members> += @begin
    Point        center;			// current circle
    Distance	 squared_radius;
@end  

@! ----------------------------------------------------------------------------
\subsection{Constructors and Destructor}

We provide several different constructors, which can be put into two
groups. The constructors in the first group, i.e. the more important
ones, build the smallest enclosing circle $mc(P)$ from a point set
$P$, given by a begin iterator and a past-the-end iterator. Usually
this would be done by a single member template, but since most
compilers do not support member templates yet, we provide specialized
constructors for C~arrays (using pointers as iterators) and for STL
sequence containers \ccc{vector<Point>} and \ccc{list<Point>}.
Actually, the constructors for a C~array and a \ccc{vector<point>} are
the same, since the random access iterator of \ccc{vector<Point>} is
implemented as \ccc{Point*}.

Both constructors of the first group copy the points into the
internal list \ccc{points}. If randomization is demanded, the points
are copied to a vector and shuffled at random, before being copied to
\ccc{points}. Finally the private member function $mc$ is called to
compute $mc(P)=mc(P,\emptyset)$.

@macro <Min_circle_2 constructors> += @begin
    // STL-like constructor for C array and vector<Point>
    CGAL_Min_circle_2( const Point* first,
		       const Point* last,
		       bool         randomize = false,
		       CGAL_Random& random    = CGAL_random,
		       I const&     i         = I())
	: ico( i)
    {
	// allocate support points' array
	support_points = new Point[ 3];

	// compute number of points
	if ( ( last-first) > 0) {

	    // store points
	    if ( randomize) {

	        // shuffle points at random
	        vector<Point> v( first, last);
		random_shuffle( v.begin(), v.end(), random);
		copy( v.begin(), v.end(), back_inserter( points)); }
	    else
		copy( first, last, back_inserter( points)); }

	// compute mc
	mc( points.end(), 0);
    }

    // STL-like constructor for list<Point>
    CGAL_Min_circle_2( list<Point>::const_iterator first,
		       list<Point>::const_iterator last,
		       bool         randomize = false,
		       CGAL_Random& random    = CGAL_random,
		       I const&     i         = I())
	: ico( i)
    {
	// allocate support points' array
	support_points = new Point[ 3];

	// compute number of points
	list<Point>::size_type n = 0;
	__distance( first, last, n, bidirectional_iterator_tag());
	if ( n > 0) {

	    // store points
	    if ( randomize) {

	        // shuffle points at random
		vector<Point> v;
		v.reserve( n);
		copy( first, last, back_inserter( v));
		random_shuffle( v.begin(), v.end(), random);
		copy( v.begin(), v.end(), back_inserter( points)); }
	    else
		copy( first, last, back_inserter( points)); }

	// compute mc
	mc( points.end(), 0);
    }
@end

The remaining constructors are actually specializations of the
previous ones, building the smallest enclosing circle for up to three
points. The idea is the following: recall that for any point set $P$
there exists $S \subseteq P$, $|S| \leq 3$ with $mc(S) = mc(P)$ (in
fact, such a set $S$ is determined by the algorithm). Once $S$ has
been computed (or given otherwise), $mc(P)$ can easily be
reconstructed from $S$ in constant time. To make this reconstruction
more convenient, a constructor is available for each size of $|S|$,
ranging from 0 to 3. For $|S|=0$, we get the default constructor,
building $mc(\emptyset)$.

@macro <Min_circle_2 constructors> += @begin

    // default constructor
    inline
    CGAL_Min_circle_2( I const& i = I())
	: ico( i), n_support_points( 0),
	  center( CGAL_ORIGIN), squared_radius( -Distance( 1))
    {
	// allocate support points' array
	support_points = new Point[ 3];

	CGAL_optimisation_postcondition( is_empty());
    }

    // constructor for one point
    inline
    CGAL_Min_circle_2( Point const& p, I const& i = I())
	: ico( i), points( 1, p), n_support_points( 1),
	  center( p), squared_radius( 0)
    {
	// allocate support points' array
	support_points = new Point[ 3];

	support_points[ 0] = p;
	CGAL_optimisation_postcondition( is_degenerate());
    }

    // constructor for two points
    inline
    CGAL_Min_circle_2( Point const& p1, Point const& p2, I const& i = I())
	: ico( i)
    {
	// allocate support points' array
	support_points = new Point[ 3];

	// store points
	points.push_back( p1);
	points.push_back( p2);

	// compute mc
	mc( points.end(), 0);
    }

    // constructor for three points
    inline
    CGAL_Min_circle_2( Point const& p1,
		       Point const& p2,
		       Point const& p3,
		       I const& i = I())
	: ico( i)
    {
	// allocate support points' array
	support_points = new Point[ 3];

	// store points
	points.push_back( p1);
	points.push_back( p2);
	points.push_back( p3);

	// compute mc
	mc( points.end(), 0);
    }
@end

The destructor only frees the memory of the support points' array.

@macro <Min_circle_2 destructor> = @begin
    inline
    ~CGAL_Min_circle_2( )
    {
	// free support points' array
	delete[] support_points;
    }
@end

@! ----------------------------------------------------------------------------
\subsection{Access Functions}

These functions are used to retrieve information about the current
status of the \ccc{CGAL_Min_circle_2<I>} object. They are all very
simple (and therefore \ccc{inline}) and mostly rely on corresponding
access functions of the data members of \ccc{CGAL_Min_circle_2<I>}.

First, we define the \ccc{number_of_...} methods.

@macro <Min_circle_2 access functions `number_of_...'> = @begin
    // #points and #support points
    inline
    int
    number_of_points( ) const
    {
	return( points.size());
    }

    inline
    int
    number_of_support_points( ) const
    {
	return( n_support_points);
    }
@end

Then, we have the access functions for points and support points.

@macro <Min_circle_2 access functions> += @begin
    // access to points and support points
    inline
    Point_iterator
    points_begin( ) const
    {
	return( points.begin());
    }

    inline
    Point_iterator
    points_end( ) const
    {
	return( points.end());
    }

    inline
    Support_point_iterator
    support_points_begin( ) const
    {
	return( support_points);
    }

    inline
    Support_point_iterator
    support_points_end( ) const
    {
	return( support_points+n_support_points);
    }

    // random access for support points    
    inline
    Point const&
    support_point( int i) const
    {
	CGAL_optimisation_precondition( (i >= 0) &&
					(i <  number_of_support_points()));
	return( support_points[ i]);
    }
@end

Finally, the access function \ccc{circle}. Note that it is
\ccc{const} to the outside world, but internally we check the
orientation of the current circle and reverse it, if necessary
(logical constness).

@macro <Min_circle_2 access functions> += @begin
    // circle
    inline
    Circle
    circle( ) const
    {
	Circle c;

	if ( is_empty())
	    return( c);
	else
	    return( Circle( center, squared_radius));
    }
@end

@! ----------------------------------------------------------------------------
\subsection{Predicates}

The predicates \ccc{is_empty} and \ccc{is_degenerate} are used in
preconditions and postconditions of some member functions. Therefore we define
them \ccc{inline} and put them in a separate macro.

@macro <Min_circle_2 predicates `is_...'> = @begin
    // is_... predicates
    inline
    bool
    is_empty( ) const
    {
	return( number_of_support_points() == 0);
    }

    inline
    bool
    is_degenerate( ) const
    {
	return( number_of_support_points() <  2);
    }
@end

The remaining predicates perform in-circle tests, based on the
corresponding predicates of class \ccc{Circle}.

@macro <Min_circle_2 predicates> = @begin
    // in-circle test predicates
    inline
    CGAL_Bounded_side
    bounded_side( Point const& p) const
    {
	return( CGAL_static_cast( CGAL_Bounded_side, 
	        CGAL_sign( ico.squared_distance( center,p) - squared_radius)));
    }

    inline
    bool
    has_on_bounded_side( Point const& p) const
    {
	return( ico.squared_distance( center, p) < squared_radius);
    }

    inline
    bool
    has_on_boundary( Point const& p) const
    {
	return( ico.squared_distance( center, p) == squared_radius);
    }

    inline
    bool
    has_on_unbounded_side( Point const& p) const
    {
	return( ico.squared_distance( center, p) > squared_radius);
    }
@end

@! ----------------------------------------------------------------------------
\subsection{Modifiers}

There is another way to build up $mc(P)$, other than by supplying
the point set $P$ at once. Namely, $mc(P)$ can be built up
incrementally, adding one point after another. If you look at the
pseudocode in the introduction, this comes quite naturally. The
modifying method \ccc{insert}, applied with point $p$ to a
\ccc{CGAL_Min_circle_2<I>} object representing $mc(P)$, computes
$mc(P \cup \{p\})$, where work has to be done only if $p$ lies
outside $mc(P)$. In this case, $mc(P \cup \{p\}) = mc(P,\{p\})$
holds, so the private member function \ccc{mc} is called with
support set $\{p\}$. After the insertion has been performed, $p$ is
moved to the front of the point list, just like in the pseudocode
in Section~\ref{sec:algo}.

@macro <Min_circle_2 modifiers> = @begin
    void
    insert( Point const& p)
    {
	// p not in current circle?
	if ( has_on_unbounded_side( p)) {

	    // p new support point
	    support_points[ 0] = p;

	    // recompute mc
	    mc( points.end(), 1);

	    // store p as the first point in list
	    points.push_front( p); }
	else

	    // append p to the end of the list
	    points.push_back( p);
    }
@end

@! ----------------------------------------------------------------------------
\subsection{Validity Check}

A \ccc{CGAL_Min_circle_2<I>} object can be checked for
validity. This means, it is checked whether (a) the circle contains
all points of its defining set $P$, (b) the circle is the smallest
circle spanned by its support set, and (c) the support set is
minimal, i.e. no support point is redundant. The function
\ccc{is_valid} is mainly intended for debugging user supplied
interface classes. If \ccc{verbose} is \ccc{true}, some messages
concerning the performed checks are written to standard error
stream. The second parameter \ccc{level} is not used, we provide it
only for consistency with interfaces of other classes.

@macro <Min_circle_2 validity check> = @begin
    bool
    is_valid( bool verbose = false, int level = 0) const
    {
	CGAL_Verbose_ostream verr( verbose);
	verr << endl;
	verr << "CGAL_Min_circle_2<I>::" << endl;
	verr << "is_valid( true, " << level << "):" << endl;
	verr << "  |P| = " << number_of_points()
	     << ", |S| = " << number_of_support_points() << endl;

	// containment check (a)
	@<Min_circle_2 containment check>

	// support set checks (b)+(c)
	@<Min_circle_2 support set checks>

	verr << "  object is valid!" << endl;
	return( true);
    }
@end

The containment check (a) is easy to perform, just a loop over all
points in \ccc{points}.

@macro <Min_circle_2 containment check> = @begin
    verr << "  a) containment check..." << flush;
    Point_iterator point_iter;
    for ( point_iter  = points_begin();
	  point_iter != points_end();
	  ++point_iter)
	if ( has_on_unbounded_side( *point_iter)) 
	    return( CGAL__optimisation_is_valid_fail( verr,
			"circle does not contain all points"));
    verr << "passed." << endl;
@end

To check the support set (b) and (c), we distinguish four cases with
respect to the number of support points, which may range from 0 to 3.

@macro <Min_circle_2 support set checks> = @begin
    verr << "  b)+c) support set checks..." << flush;
    switch( number_of_support_points()) {

      case 0:
	@<Min_circle_2 check no support point>
	break;

      case 1:
	@<Min_circle_2 check one support point>
	break;

      case 2: {
	@<Min_circle_2 check two support points> }
	break;

      case 3: {
	@<Min_circle_2 check three support points> }
	break;

      default:
        return( CGAL__optimisation_is_valid_fail( verr,
		    "illegal number of support points, not between 0 and 3."));
    };
    verr << "passed." << endl;
@end

The case of no support point happens if and only if the defining
point set $P$ is empty.

@macro <Min_circle_2 check no support point> = @begin
    if ( ! is_empty())
	return( CGAL__optimisation_is_valid_fail( verr,
		    "P is nonempty, but there are no support points."));
@end

If the smallest enclosing circle has one support point $p$, it must
be equal to that point, i.e.\ its center must be $p$ and its radius
$0$.

@macro <Min_circle_2 check one support point> = @begin
    if ( ( center != support_point( 0)    ) ||
	 ( ! CGAL_is_zero( squared_radius)) )
	return( CGAL__optimisation_is_valid_fail( verr,
       "circle differs from the circle spanned by its single support point."));
@end

In case of two support points $p,q$, these points must form a diameter
of the circle. The support set $\{p,q\}$ is minimal if and only if
$p,q$ are distinct.

The diameter property is checked as follows. If $p$ and $q$ both lie
on the circle's boundary and if $p$, $q$ (knowing they are distinct)
and the circle's center are collinear, then $p$ and $q$ form a
diameter of the circle.

@macro <Min_circle_2 check two support points> = @begin
    Point const& p( support_point( 0)),
		 q( support_point( 1));

    // p equals q?
    if ( p == q)
	return( CGAL__optimisation_is_valid_fail( verr,
		    "the two support points are equal."));

    // segment(p,q) is not diameter?
    if ( ( ! has_on_boundary( p)                           ) ||
	 ( ! has_on_boundary( q)                           ) ||
	 ( ico.orientation( p, q, center) != CGAL_COLLINEAR) )
	return( CGAL__optimisation_is_valid_fail( verr,
		  "circle does not have its two support points as diameter."));
@end

If the number of support points is three (and they are distinct and not
collinear), the circle through them is unique, and must therefore equal
the current circle stored in \ccc{center} and \ccc{squared_radius}. It
is the smallest one containing the three points if and only if the
center of the circle lies inside or on the boundary of the triangle
defined by the three points. The support set is minimal only if the
center lies properly inside the triangle.

Both triangle properties are checked by comparing the orientatons of
three point triples, each containing two of the support points and the
center of the current circle, resp. If one of these orientations equals
\ccc{CGAL_COLLINEAR}, the center lies on the boundary of the triangle.
Otherwise, if two triples have opposite orientations, the center is not
contained in the triangle.

@macro <Min_circle_2 check three support points> = @begin
    Point const& p( support_point( 0)),
		 q( support_point( 1)),
		 r( support_point( 2));

    // p, q, r not pairwise distinct?
    if ( ( p == q) || ( q == r) || ( r == p))
	return( CGAL__optimisation_is_valid_fail( verr,
		    "at least two of the three support points are equal."));

    // p, q, r collinear?
    if ( ico.orientation( p, q, r) == CGAL_COLLINEAR)
	return( CGAL__optimisation_is_valid_fail( verr,
		    "the three support points are collinear."));

    // current circle not equal the unique circle through p,q,r ?
    Point cp( ico.circumcenter( p, q, r));
    Distance dp( ico.squared_distance( cp, p));
    Distance dq( ico.squared_distance( cp, q));
    Distance dr( ico.squared_distance( cp, r));
    if ( ( center         != cp) ||
         ( squared_radius != dp) ||
         ( squared_radius != dq) ||
         ( squared_radius != dr) )
	return( CGAL__optimisation_is_valid_fail( verr,
         "circle is not the unique circle through its three support points."));

    // circle's center on boundary of triangle(p,q,r)?
    CGAL_Orientation o_pqz = ico.orientation( p, q, center);
    CGAL_Orientation o_qrz = ico.orientation( q, r, center);
    CGAL_Orientation o_rpz = ico.orientation( r, p, center);
    if ( ( o_pqz == CGAL_COLLINEAR) ||
	 ( o_qrz == CGAL_COLLINEAR) ||
	 ( o_rpz == CGAL_COLLINEAR) )
	return( CGAL__optimisation_is_valid_fail( verr,
		    "one of the three support points is redundant."));

    // circle's center not inside triangle(p,q,r)?
    if ( ( o_pqz != o_qrz) || ( o_qrz != o_rpz) || ( o_rpz != o_pqz))
	return( CGAL__optimisation_is_valid_fail( verr,
    "circle's center is not in the convex hull of its three support points."));
@end

@! ----------------------------------------------------------------------------
\subsection{I/O}

@macro <Min_circle_2 I/O operators> = @begin
    friend
    ostream&
    operator << ( ostream& os, CGAL_Min_circle_2<I> const& min_circle)
    {
	switch ( CGAL_get_mode( os)) {

	  case CGAL_IO::PRETTY:
	    os << endl;
	    os << "CGAL_Min_circle_2( |P| = " << min_circle.number_of_points()
	       << ", |S| = " << min_circle.number_of_support_points() << endl;
	    os << "  P = {" << endl;
	    os << "    ";
	    copy( min_circle.points_begin(), min_circle.points_end(),
		  ostream_iterator<Point>( os, ",\n    "));
	    os << "}" << endl;
	    os << "  S = {" << endl;
	    os << "    ";
	    copy( min_circle.support_points_begin(),
		  min_circle.support_points_end(),
		  ostream_iterator<Point>( os, ",\n    "));
	    os << "}" << endl;
	    os << "  min_circle = " << min_circle.circle() << endl;
	    os << ")" << endl;
	    break;

	  case CGAL_IO::ASCII:
	    copy( min_circle.points_begin(), min_circle.points.end(),
		  ostream_iterator<Point>( os, "\n"));
	    break;

	  case CGAL_IO::BINARY:
	    copy( min_circle.points_begin(), min_circle.points.end(),
		  ostream_iterator<Point>( os));
	    break;

	  default:
	    CGAL_optimisation_assertion_msg( false,
					     "CGAL_get_mode( os) invalid!");
	    break; }

	return( os);
    }

    friend
    istream&
    operator >> ( istream& is, CGAL_Min_circle_2<I>& min_circle)
    {
	switch ( CGAL_get_mode( is)) {
	  case CGAL_IO::PRETTY:
	    cerr << endl;
	    cerr << "Stream must be in ascii or binary mode" << endl;
	    break;
	  case CGAL_IO::ASCII:
	  case CGAL_IO::BINARY:
	    min_circle.points.erase( min_circle.points.begin(),
				     min_circle.points.end());
	    copy( istream_iterator<Point, ptrdiff_t>( is),
		  istream_iterator<Point, ptrdiff_t>(),
		  back_inserter( min_circle.points));
	    min_circle.mc( min_circle.points.end(), 0);
	    break;
	  default:
	    CGAL_optimisation_assertion_msg( false, "CGAL_IO::mode invalid!");
	    break; }

	return( is);
    }
@end

@! ----------------------------------------------------------------------------
\subsection{Private Member Function {\ccFont compute\_circle}}

This is the method for computing the basic case $\mc(\emptyset,B)$,
the set $B$ given by the first \ccc{n_support_points} in the array
\ccc{support_points}. It is realized by a simple case analysis,
noting that $|B| \leq 3$.

@macro <Min_circle_2 private member function `compute_circle'> = @begin
    // compute_circle
    inline
    void
    compute_circle( )
    {
	switch ( n_support_points) {
	  case 3:
	    center         = ico.circumcenter( support_points[ 0],
					       support_points[ 1],
					       support_points[ 2]);
	    squared_radius = ico.squared_distance( center,
						   support_points[ 0]);
            break;
	  case 2:
	    center         = ico.midpoint( support_points[ 0],
					   support_points[ 1]);
	    squared_radius = ico.squared_distance( center,
						   support_points[ 0]);
	    break;
	  case 1:
	    center         = support_points[ 0];
	    squared_radius = Distance( 0);
	    break;
	  case 0:
	    center         = Point( CGAL_ORIGIN);
	    squared_radius = -Distance( 1);
	    break;
	  default:
	    CGAL_optimisation_assertion( ( n_support_points >= 0) &&
					 ( n_support_points <= 3) ); }
    }
@end

@! ----------------------------------------------------------------------------
\subsection{Private Member Function {\ccFont mc}}

This function computes the general circle $mc(P,B)$, where $P$
contains the points in the range
$[$\ccc{points.begin()}$,$\ccc{last}$)$ and $B$ is given by the first
\ccc{n_sp} support points in the array \ccc{support_points}. The
function is directly modelled after the pseudocode above.

@macro <Min_circle_2 private member function `mc'> = @begin
    void
    mc( Point_iterator const& last, int n_sp)
    {
	// compute circle through support points
	n_support_points = n_sp;
	compute_circle( );
	if ( n_sp == 3) return;

	// test first n points
	list<Point>::iterator  point_iter( points.begin());
	for ( ; last != point_iter; ) {
	    Point const& p( *point_iter);

	    // p not in current circle?
	    if ( has_on_unbounded_side( p)) {

		// recursive call with p as additional support point
		support_points[ n_sp] = p;
		mc( point_iter, n_sp+1);

		// move current point to front
		if ( point_iter != points.begin()) {		// p not first?
		    points.push_front( p);
		    points.erase( point_iter++); }
		else
		    ++point_iter; }
	    else
		++point_iter; }
    }
@end

@! ============================================================================
@! Test
@! ============================================================================

\clearpage
\section{Test}

We test \ccc{CGAL_Min_circle_2} with the default interface class for
optimisation algorithms, using exact arithmetic, i.e.\ homogeneous
representation and number type \ccc{integer}.

@macro <Min_circle_2 test (includes and typedefs)> = @begin
    #include <CGAL/Integer.h>
    #include <CGAL/Homogeneous.h>
    #include <CGAL/Optimisation_default_interface_2.h>
    #include <CGAL/Min_circle_2.h>
    #include <CGAL/Random.h>
    #include <CGAL/IO/Verbose_ostream.h>
    #include <algo.h>
    #include <assert.h>
    #include <fstream.h>

    #include <LEDA/REDEFINE_NAMES.h>
    typedef  integer				       NT;
    #include <LEDA/UNDEFINE_NAMES.h>

    typedef  CGAL_Homogeneous<NT>		       R;
    typedef  CGAL_Optimisation_default_interface_2<R>  I;
    typedef  CGAL_Min_circle_2<I>		       Min_circle;
    typedef  Min_circle::Point			       Point;
    typedef  Min_circle::Circle			       Circle;
@end

We call each function of class \ccc{CGAL_Min_circle_2<I>} at least once
to ensure code coverage. The command line option \ccc{-verbose}
enables verbose output.

@macro <Min_circle_2 test (verbose option)> = @begin
    bool  verbose = false;
    if ( ( argc > 1) && ( strcmp( argv[ 1], "-verbose") == 0)) {
        verbose = true;
	--argc;
	++argv; }
    CGAL_Verbose_ostream verr( verbose);
@end

@macro <Min_circle_2 test (code coverage)> = @begin
    // generate 10 points at random
    CGAL_Random	 random_x, random_y;
    Point	 random_points[ 10];
    int		 i;
    verr << "10 random points from [0,100)^2:" << endl;
    for ( i = 0; i < 10; ++i)
	random_points[ i] = Point( random_x( 100), random_y( 100));
    if ( verbose)
	for ( i = 0; i < 10; ++i)
	    cerr << i << ": " << random_points[ i] << endl;

    verr << endl << "default constructor...";
    {
	Min_circle  mc;
	bool  is_valid = mc.is_valid( verbose);
	bool  is_empty = mc.is_empty();
	assert( is_valid); 
	assert( is_empty);
    }

    verr << endl << "one point constructor...";
    {
	Min_circle  mc( random_points[ 0]);
	bool  is_valid      = mc.is_valid( verbose);
	bool  is_degenerate = mc.is_degenerate();
	assert( is_valid);
	assert( is_degenerate);
    }

    verr << endl << "two points constructor...";
    {
	Min_circle  mc( random_points[ 1],
			random_points[ 2]);
	bool  is_valid = mc.is_valid( verbose);
	assert( is_valid);
    }

    verr << endl << "three points constructor...";
    {    
	Min_circle  mc( random_points[ 3],
			random_points[ 4],
			random_points[ 5]);
	bool  is_valid = mc.is_valid( verbose);
	assert( is_valid);
    }

    verr << endl << "Point* constructor (without randomization)...";
    {
	Min_circle  mc( random_points, random_points+9);
	bool  is_valid = mc.is_valid( verbose);
	assert( is_valid);
    }

    verr << endl << "Point* constructor (with randomization)...";
    Min_circle mc( random_points, random_points+9, true);
    {
	bool  is_valid = mc.is_valid( verbose);
	assert( is_valid);
    }

    verr << endl << "list<Point>::const_iterator constructor...";
    {
	Min_circle  mc2( mc.points_begin(), mc.points_end(), true);
	bool  is_valid = mc2.is_valid( verbose);
	assert( is_valid);
	assert( mc.circle() == mc2.circle());
    }

    verr << endl << "#points...";
    {
	int  number_of_points = mc.number_of_points();
	assert( number_of_points == 9);
    }

    verr << endl << "points access already called above.";

    verr << endl << "support points access...";
    {
	Point  support_point;
        Min_circle::Support_point_iterator  iter( mc.support_points_begin());
        for ( i = 0; i < mc.number_of_support_points(); ++i, ++iter) {
	    support_point = mc.support_point( i);
	    assert( support_point == *iter); }
	Min_circle::Support_point_iterator  end_iter( mc.support_points_end());
        assert( iter == end_iter);
    }

    verr << endl << "circle access...";
    Circle  circle( mc.circle());

    verr << endl << "in-circle predicates...";
    {
	Point              p;
	CGAL_Bounded_side  bounded_side;
	bool               has_on_bounded_side;
	bool		   has_on_boundary;
	bool		   has_on_unbounded_side;
	for ( i = 0; i < 9; ++i) {
	    p = random_points[ i];
	    bounded_side          = mc.bounded_side( p);
	    has_on_bounded_side   = mc.has_on_bounded_side( p);
	    has_on_boundary       = mc.has_on_boundary( p);
	    has_on_unbounded_side = mc.has_on_unbounded_side( p);
	assert( bounded_side != CGAL_ON_UNBOUNDED_SIDE);
	assert( has_on_bounded_side || has_on_boundary);
	assert( ! has_on_unbounded_side); }
    }

    verr << endl << "is_... predicates already called above.";

    verr << endl << "modifiers...";
    mc.insert( random_points[ 9]);
    {
	bool  is_valid = mc.is_valid( verbose);
	assert( is_valid);
    }

    verr << endl << "validity check already called several times.";

    verr << endl << "I/O...";
    {
	verr << endl << "  writing `test_Min_circle_2.ascii'...";
	ofstream os( "test_Min_circle_2.ascii");
	CGAL_set_ascii_mode( os);
	os << mc;
    }
    {
	verr << endl << "  writing `test_Min_circle_2.pretty'...";
	ofstream os( "test_Min_circle_2.pretty");
	CGAL_set_pretty_mode( os);
	os << mc;
    }
    {
	verr << endl << "  writing `test_Min_circle_2.binary'...";
	ofstream os( "test_Min_circle_2.binary");
	CGAL_set_binary_mode( os);
	os << mc;
    }
    {
	verr << endl << "  reading `test_Min_circle_2.ascii'...";
	Min_circle mc_in;
	ifstream is( "test_Min_circle_2.ascii");
	CGAL_set_ascii_mode( is);
	is >> mc_in;
	assert( mc.circle() == mc_in.circle());
    }
    verr << endl;
@end

In addition, some data files can be given as command line
arguments. A data file contains pairs of \ccc{int}, namely the x- and
y-coordinates of a set of points. The first number in the file is the
number of points. A short description of the test set is given at the
end of each file.

@macro <Min_circle_2 test (external test sets)> = @begin
    while ( argc > 1) {

	// read points from file
	verr << endl << "input file: `" << argv[ 1] << "'" << flush;

	list<Point>  points;
	int          n, x, y;
	ifstream     in( argv[ 1]);
	in >> n;
	assert( in);
	for ( i = 0; i < n; ++i) {
	    in >> x >> y;
	    assert( in);
	    points.push_back( Point( x, y)); }

	// compute and check min_circle
	Min_circle  mc2( points.begin(), points.end());
	bool  is_valid = mc2.is_valid( verbose);
	assert( is_valid);

	// next file
	--argc;
	++argv; }
@end


@! ==========================================================================
@! Files
@! ==========================================================================

\clearpage
\section{Files}

@file <Min_circle_2.h> = @begin
    @<Min_circle_2 header>("include/CGAL/Min_circle_2.h")

    #ifndef CGAL_MIN_CIRCLE_2_H
    #define CGAL_MIN_CIRCLE_2_H

    // Class declaration
    // =================
    @<Min_circle_2 declaration>

    // Class interface
    // ===============
    // includes
    #ifndef CGAL_RANDOM_H
    #  include <CGAL/Random.h>
    #endif
    #ifndef CGAL_OPTIMISATION_ASSERTIONS_H
    #  include <CGAL/optimisation_assertions.h>
    #endif
    #ifndef CGAL_OPTIMISATION_MISC_H
    #  include <CGAL/optimisation_misc.h>
    #endif
    #include <list.h>
    #include <vector.h>
    #include <algo.h>
    #include <iostream.h>

    @<Min_circle_2 interface>

    #endif // CGAL_MIN_CIRLCE_2_H

    @<end of file line>
@end

@file <test_Min_circle_2.C> = @begin
    @<Min_circle_2 header>("test/test_Min_circle_2.C")

    @<Min_circle_2 test (includes and typedefs)>

    int
    main( int argc, char* argv[])
    {
        // command line options
	// --------------------
	// option `-verbose'
	@<Min_circle_2 test (verbose option)>

	// code coverage
	// -------------
	@<Min_circle_2 test (code coverage)>

	// external test sets
	// -------------------
	@<Min_circle_2 test (external test sets)>
    }

    @<end of file line>
@end

@i file_header.awlib
 
@macro <Min_circle_2 header>(1) many = @begin
    @<file header>("2D Smallest Enclosing Circle",@1,"Min_circle_2",
		   "Bernd G�rtner, Sven Sch�nherr (sven@@inf.fu-berlin.de)",
		   "$Revision$","$Date$")
@end

@! ============================================================================
@! Bibliography
@! ============================================================================

\clearpage
\bibliographystyle{plain}
\bibliography{geom,cgal}

@! ===== EOF ==================================================================
